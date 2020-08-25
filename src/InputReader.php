<?php

namespace OnzaMe\Helpers;

use OnzaMe\Helpers\Exceptions\Exception;

/**
 * Class InputReader
 * @package OnzaMe\Helpers\Helpers
 */
class InputReader {
    private static $instance;

    /**
     * Factory for InputReader
     *
     * @param string $inputContents
     *
     * @return InputReader
     */
    public static function instance($inputContents = null) {
        if (self::$instance === null) {
            self::$instance = new InputReader($inputContents);
        }

        return self::$instance;
    }

    protected $handle;

    /**
     * InputReader constructor.
     *
     * @param string $inputContents
     */
    public function __construct($inputContents = null) {
        // Open up a new memory handle
        $this->handle = fopen('php://memory', 'r+');

        // If we haven't specified the input contents (in case you're reading it from somewhere else like a framework), then we'll read it again
        if ($inputContents === null) {
            $inputContents = file_get_contents('php://input');
        }

        // Write all the contents of php://input to our memory handle
        fwrite($this->handle, $inputContents);

        // Seek back to the start if we're reading anything
        fseek($this->handle, 0);
    }

    public function getHandle() {
        return $this->handle;
    }

    /**
     * Wrapper for fseek
     *
     * @param int $offset
     * @param int $whence
     *
     * @return InputReader
     *
     * @throws \Exception
     */
    public function seek($offset, $whence = SEEK_SET) {
        if (fseek($this->handle, $offset, $whence) !== 0) {
            throw new Exception(__('http/errors.could_not_use_memory_handle', ['replace' => 'fseek']));
        }

        return $this;
    }

    public function read($length) {
        $read = fread($this->handle, $length);

        if ($read === false) {
            throw new Exception(__('http/errors.could_not_use_memory_handle', ['replace' => 'fread']));
        }

        return $read;
    }

    public function readAll($buffer = 8192) {
        $reader = '';

        $this->seek(0); // make sure we start by seeking to offset 0

        while (!$this->eof()) {
            $reader .= $this->read($buffer);
        }

        return $reader;
    }

    public function eof() {
        return feof($this->handle);
    }
}
