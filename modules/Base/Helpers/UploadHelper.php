<?php

namespace Modules\Base\Helper;

class UploadHelper
{
    /**
     * @var string
     */
    public $destination = '/images/';

    /**
     * @var string
     */
    public $fileName = 'file.txt';

    /**
     * @var string
     */
    public $maxSize = '1048576'; // bytes (1048576 bytes = 1 meg)

    /**
     * @var array
     */
    public $allowedExtensions = array('image/jpeg','image/png','image/gif'); // mime types

    /**
     * @var bool
     */
    public $printError = TRUE;

    /**
     * @var string
     */
    public $error = '';

    /**
     * @param $newDestination
     */
    public function setDestination($newDestination)
    {
        $this->destination = $newDestination;
    }

    /**
     * @param $newFileName
     */
    public function setFileName($newFileName)
    {
        $this->fileName = $newFileName;
    }

    /**
     * @param $newValue
     */
    public function setPrintError($newValue)
    {
        $this->printError = $newValue;
    }

    /**
     * @param $newSize
     */
    public function setMaxSize($newSize)
    {
        $this->maxSize = $newSize;
    }

    /**
     * @param $newExtensions
     */
    public function setAllowedExtensions($newExtensions)
    {
        if (is_array($newExtensions)) {
            $this->allowedExtensions = $newExtensions;
        }
        else {
            $this->allowedExtensions = array($newExtensions);
        }
    }

    /**
     * @param $file
     */
    public function upload($file)
    {

        $this->validate($file);

        if ($this->error) {
            if ($this->printError) print $this->error;
        }
        else {
            move_uploaded_file($file['tmp_name'][0], $this->destination.$this->fileName) or $this->error .= 'Destination Directory Permission Problem.<br />';
            if ($this->error && $this->printError) print $this->error;
        }
    }

    /**
     * @param $file
     */
    public function delete($file)
    {

        if (file_exists($file)) {
            unlink($file) or $this->error .= 'Destination Directory Permission Problem.<br />';
        }
        else {
            $this->error .= 'File not found! Could not delete: '.$file.'<br />';
        }

        if ($this->error && $this->printError) print $this->error;
    }

    /**
     * @param $file
     */
    public function validate($file)
    {

        $error = '';

        //check file exist
        if (empty($file['name'][0])) $error .= 'No file found.<br />';
        //check allowed extensions
        if (!in_array($this->getExtension($file),$this->allowedExtensions)) $error .= 'Extension is not allowed.<br />';
        //check file size
        if ($file['size'][0] > $this->maxSize) $error .= 'Max File Size Exceeded. Limit: '.$this->maxSize.' bytes.<br />';

        $this->error = $error;
    }

    /**
     * @param $file
     * @return mixed
     */
    public function getExtension($file)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $ext = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        return $ext;
    }
}