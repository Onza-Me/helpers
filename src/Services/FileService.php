<?php
/**
 * Created by PhpStorm.
 * User: acrossoffwest
 * Date: 9/6/18
 * Time: 10:53 AM
 */

namespace OnzaMe\Helpers\Services;


use OnzaMe\Helpers\Exceptions\UnproccessableHttpRequestException;
use OnzaMe\Helpers\InputReader;
use OnzaMe\Helpers\ParseInputStream;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * Class FileService
 * @package App\Services
 */
class FileService
{
    /** @var string|null */
    protected $validationType;

    /**
     * FileService constructor.
     * @param string|null $validationType
     */
    public function __construct($validationType = null)
    {
        $this->setValidationType($validationType);
    }

    /**
     * @param string|null $validationType
     */
    public function setValidationType($validationType = null)
    {
        $this->validationType = $validationType;
    }

    /**
     * @return string
     */
    public function getValidationType()
    {
        return $this->validationType;
    }

    protected function validate($file, $validateType = null)
    {
        $validateType = !empty($validateType) ? $validateType : $this->getValidationType();
        $classTemplate = 'App\Services\Validates\File\:typeValidate';
        $class = str_replace(':type', ucfirst($validateType), $classTemplate);
        if (!class_exists($class)) {
            throw new Exception('The validate type: '.$validateType.' was not found.');
        }
        return (new $class($this))->validate($file);
    }

    /**
     * @param Request $request
     * @param User $user
     * @param string $path
     * @param string $parameterKey
     * @param string|null $disk
     * @return Collection
     * @throws \Exception
     * @throws \Throwable
     */
    public function uploadFiles(Request $request, User $user, string $path = '/', string $parameterKey = 'pictures', string $disk = null): Collection
    {
        $result = collect([]);

        if (!$request->hasFile($parameterKey)) {
            return $result;
        }

        $files = $request->file($parameterKey);

        if (is_array($files)) {
            foreach ($files as $file) {
                $result->push($this->upload($file, $user, $path, $disk, $parameterKey));
            }
        } elseif (is_a($files, UploadedFile::class)) {
            $result->push($this->upload($files));
        }

        return $result;
    }

    /**
     * @param UploadedFile|Request $uploadedFile
     * @param User|string $user
     * @param string $path
     * @param string $disk
     * @param string $parameterKey
     * @throws \Exception
     * @throws \Throwable
     */
    public function upload($uploadedFile, User $user, string $path = '/', string $disk = null, $parameterKey = 'file') : \App\Models\File
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $this->getUploadedFile($uploadedFile, $parameterKey);

        $ext = mb_strtolower(str_replace('.', '', $this->extension($uploadedFile)));
        throw_if(!in_array($ext, config('files.allowed_extensions')), new PreconditionFailureException([
            $parameterKey => __('http/errors.file_fromat_not_allowed', ['ext' => $ext])
        ]));

        $file = $this->store([
            'user_id' => $user->id,
            'disk' => empty($disk) ? config('filesystems.default') : $disk,
            'path' => $path
        ]);
        throw_if(empty($file), new PreconditionFailureException());

        $file->name = $file->id.'-'.time().$this->extension($uploadedFile);
        $file->save();

        $full_path_source = $uploadedFile->getPathname();
        $full_path_dest = Storage::disk($disk)->getDriver()->getAdapter()->applyPathPrefix($path.$file->name);

        if (!File::exists(dirname($full_path_dest))) {
            File::makeDirectory(dirname($full_path_dest), null, true);
        }

        File::copy($full_path_source, $full_path_dest);

        if (!File::exists($full_path_dest)) {
            $this->delete($file->id);
            throw new Exception('File entity not created', 412);
        }

        if (!empty($this->getValidationType())) {
            try {
                $this->validate($file, $this->getValidationType());
            } catch (Exception $exception) {
                $this->delete($file->id);
                throw $exception;
            }
        }

        return $file;
    }

    /**
     * @param \App\Models\File|string $file
     * @return \Intervention\Image\Image
     */
    public function makeImage($file): \Intervention\Image\Image
    {
        $file = $this->getEntity($file);

        $absloutePath = $this->getAbsolutePath($file);

        return \Image::make($absloutePath);
    }

    /**
     * @param \App\Models\File|string $file
     * @param array $rules
     */
    public function optimizeImageSize($file, $rules, $returnNewEntity = false)
    {
        $file = $this->getEntity($file);

        $this->validate($file, 'image');

        if (empty($rules)) {
            throw new Exception('The optimize rules was empty.');
        }

        $image = $this->makeImage($file);

        $map = mapper_array($rules);

        if (!empty($map->get('max'))) {
            if (!empty($map->get('max.height')) && $image->width() > $map->get('max.height')) {
                $image->heighten($map->get('max.height'));
            }
            if (!empty($map->get('max.width')) && $image->width() > $map->get('max.width')) {
                $image->widen($map->get('max.width'));
            };
        }

        if (!empty($map->get('min'))) {
            if (!empty($map->get('min.height')) && $image->height() < $map->get('min.height')) {
                $image->heighten($map->get('min.height'));
            }
            if (!empty($map->get('min.width')) && $image->height() < $map->get('min.width')) {
                $image->widen($map->get('min.width'));
            }
        }

        if (!$returnNewEntity) {
            $image->save();
            return $file;
        }

        $filepath = $this->getTmpFilepath();
        $image->save($filepath);

        return $this->upload($this->getUploadedFileBy($filepath), $file->user);
    }

    /**
     * @param UploadedFile|Request $request
     * @param string $parameterKey
     * @return UploadedFile
     * @throws Exception
     */
    public function getUploadedFile($request, $parameterKey = 'file')
    {
        if (is_a($request, UploadedFile::class)) {
            return $request;
        }

        $uploadedFile = empty($request->get($parameterKey)) ? $request->file($parameterKey) : $request->get($parameterKey);

        if (empty($uploadedFile)) {
            $uploadedFile = $this->getUploadedFileBy($this->uploadBinaryFileFromStream($parameterKey));
        }

        if (empty($uploadedFile)) {
            throw new UnproccessableHttpRequestException('', '', [
                $parameterKey => 'Something went wrong'
            ]);
        }

        return $uploadedFile;
    }

    /**
     * @param $parameterKey
     * @return string
     * @throws UnproccessableHttpRequestException
     */
    public function uploadBinaryFileFromStream($parameterKey): string
    {
        $params = [];
        new ParseInputStream($params);
        $fileBinaryData = InputReader::instance()->readAll();

        if (empty($fileBinaryData)) {
            throw new UnproccessableHttpRequestException('', '', [
                $parameterKey => 'Empty file'
            ]);
        }

        $fileTmpPath = $this->getTmpFilepath();
        file_put_contents($fileTmpPath, $fileBinaryData);

        return $fileTmpPath;
    }
    /**
     * @param string $absolutePath
     * @return UploadedFile
     */
    public function getUploadedFileBy(string $absolutePath): UploadedFile
    {
        $explodedPath = explode('/', $absolutePath);
        $fileTmp = new \Symfony\Component\HttpFoundation\File\UploadedFile(
            $absolutePath,
            $explodedPath[count($explodedPath) - 1]
        );

        return UploadedFile::createFromBase($fileTmp);
    }

    public function getTmpFilepath()
    {
        return tempnam(sys_get_temp_dir(), 'Domda_Upload_File_'.time());
    }

    protected function extension(UploadedFile $uploadedFile)
    {
        $ext = null;
        if (!empty($uploadedFile->getClientOriginalExtension())) {
            $ext =  $uploadedFile->getClientOriginalExtension();
        } else {
            $ext = mime_type_to_extension($uploadedFile->getMimeType());
        }

        return '.'.$ext;
    }

    public function getRelativePath(\App\Models\File $file): string
    {
        return $file->path.$file->name;
    }

    /**
     * Get absolute path for file model
     *
     * @param File|string $file
     * @return string
     */
    public function getAbsolutePath($file)
    {
        /** @var \App\Models\File $file */
        $file = $this->getEntity($file);

        return config('filesystems.disks.'.$file->disk.'.root').$this->getRelativePath($file);
    }

    public function exists(string $absolutepath)
    {
        return file_exists($absolutepath);
    }

    public function getRepositoryClass() : string
    {
        return FileRepository::class;
    }
}
