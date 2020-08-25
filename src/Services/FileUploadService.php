<?php
/**
 * Created by PhpStorm.
 * User: acrossoffwest
 * Date: 9/6/18
 * Time: 11:29 AM
 */

namespace OnzaMe\Helpers\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    /**
     * @param string $url
     * @param string|null $filepath
     * @return false|string
     * @throws \Exception
     */
    public function downloadFile(string $url, string $filepath = null)
    {
        $filepath = $this->prepareFilepath($filepath);
        try {
            $file = fopen($url, 'rb');

            if ($file) {
                $newFile = fopen($filepath, 'wb');
                if ($newFile) {
                    while (!feof($file)) {
                        fwrite($newFile, fread($file, 1024 * 8), 1024 * 8);
                    }
                }
            }

            if ($file) {
                fclose($file);
            }
            if ($newFile) {
                fclose($newFile);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }

        return $filepath;
    }

    /**
     * @param string $url
     * @return UploadedFile
     * @throws \Exception
     */
    public function getUploadedFileByExternalUrl(string $url) : UploadedFile
    {
        $uploadedFilepath = $this->downloadFile($url);
        return new UploadedFile(
            $uploadedFilepath,
            'background-generate-service.jpg',
            null,
            null,
            null,
            true
        );
    }

    private function prepareFilepath(string $filepath = null)
    {
        return $filepath ?? tempnam(sys_get_temp_dir(), 'file-upload-service-');
    }

    /**
     * @param string $url
     * @return bool|string
     */
    public function getFinalUrl(string $url)
    {
        do {
            $context = stream_context_create(
                [
                    "http" => [
                        "follow_location" => false,
                    ]
                ]
            );

            $result = file_get_contents($url, false, $context);

            $pattern = "/^Location:\s*(.*)$/i";
            $location_headers = preg_grep($pattern, $http_response_header);

            if (!empty($location_headers) &&
                preg_match($pattern, array_values($location_headers)[0], $matches)) {
                $url = $matches[1];
                $repeat = true;
            } else {
                $repeat = false;
            }
        } while ($repeat);

        return $result;
    }
}
