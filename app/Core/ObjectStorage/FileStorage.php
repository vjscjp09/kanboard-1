<?php

namespace Core\ObjectStorage;

/**
 * Local File Storage
 *
 * @package  ObjectStorage
 * @author   Frederic Guillot
 */
class FileStorage implements ObjectStorageInterface
{
    /**
     * Base path
     *
     * @access private
     * @var string
     */
    private $path = '';

    /**
     * Constructor
     *
     * @access public
     * @param  string  $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Fetch object contents
     *
     * @access public
     * @param  string  $key
     * @return string
     */
    public function get($key)
    {
        $filename = $this->path.DIRECTORY_SEPARATOR.$key;

        if (! file_exists($filename)) {
            throw new ObjectStorageException('File not found: '.$filename);
        }

        return file_get_contents($filename);
    }

    /**
     * Save object
     *
     * @access public
     * @param  string  $key
     * @param  string  $blob
     * @return string
     */
    public function put($key, &$blob)
    {
        $this->createFolder($key);

        if (file_put_contents($this->path.DIRECTORY_SEPARATOR.$key, $blob) === false) {
            throw new ObjectStorageException('Unable to write the file: '.$this->path.DIRECTORY_SEPARATOR.$key);
        }
    }

    /**
     * Output directly object content
     *
     * @access public
     * @param  string  $key
     */
    public function output($key)
    {
        $filename = $this->path.DIRECTORY_SEPARATOR.$key;

        if (! file_exists($filename)) {
            throw new ObjectStorageException('File not found: '.$filename);
        }

        return readfile($filename);
    }

    /**
     * Move local file to object storage
     *
     * @access public
     * @param  string  $src_filename
     * @param  string  $key
     * @return boolean
     */
    public function moveFile($src_filename, $key)
    {
        $this->createFolder($key);
        $dst_filename = $this->path.DIRECTORY_SEPARATOR.$key;

        if (! rename($src_filename, $dst_filename)) {
            throw new ObjectStorageException('Unable to move the file: '.$src_filename.' to '.$dst_filename);
        }

        return true;
    }

    /**
     * Move uploaded file to object storage
     *
     * @access public
     * @param  string  $filename
     * @param  string  $key
     * @return boolean
     */
    public function moveUploadedFile($filename, $key)
    {
        $this->createFolder($key);
        return move_uploaded_file($filename, $this->path.DIRECTORY_SEPARATOR.$key);
    }

    /**
     * Remove object
     *
     * @access public
     * @param  string  $key
     * @return boolean
     */
    public function remove($key)
    {
        $filename = $this->path.DIRECTORY_SEPARATOR.$key;

        if (file_exists($filename)) {
            return unlink($filename);
        }

        return false;
    }

    /**
     * Create object folder
     *
     * @access private
     * @param  string  $key
     */
    private function createFolder($key)
    {
        $folder = $this->path.DIRECTORY_SEPARATOR.dirname($key);

        if (! is_dir($folder) && ! mkdir($folder, 0755, true)) {
            throw new ObjectStorageException('Unable to create folder: '.$folder);
        }
    }
}
