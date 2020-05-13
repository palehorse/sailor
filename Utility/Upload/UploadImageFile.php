<?php
namespace Sailor\Utility\Upload;

class UploadImageFile
{
    const KILO = 1000;

    /** @var array */
    private $file;

    /** @var string */
    private $exts = [];

    /** @var float|integer */ 
    private $maxSize;

    /** @var integer */
    private $maxWidth;

    /** @var integer */
    private $maxHeight;

    /** @var integer */
    private $minWidth;

    /** @var integer */
    private $minHeight;

    /** @var integer */
    private $width;

    /** @var integer */
    private $height;

    public function __construct(array $file, array $exts = [], $maxSize = null, $exactWidth = null, $exactHeight = null, $maxWidth = null, $maxHeight = null)
    {
        $this->file = $file;
        $this->exts = $exts;
        $this->maxSize = $maxSize;
        $this->exactWidth = $exactWidth;
        $this->exactHeight = $exactHeight;
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;

        list($this->width, $this->height) = getimagesize($this->file['tmp_name']);
    }

    public function checkUploadSuccess()
    {
        return $this->file['error'] == UPLOAD_ERR_OK;
    }

    public function checkFileExists()
    {
        return $this->file['error'] == UPLOAD_ERR_NO_FILE;
    }

    public function checkExts()
    {
        if (!empty($this->exts)) {
            return preg_match('\.' . implode('|', $this->exts) . '$', $this->file['name']);
        }
        return true;
    }

    public function checkMaxSize()
    {
        if (!empty($this->maxSize)) {
            return $this->file['size'] <= $this->maxSize * self::KILO;
        }
        return true;
    }

    public function checkExactWidth()
    {
        if (!empty($this->exactWidth)) {
            return $this->width == $this->exactWidth;
        }
        return true;
    }

    public function checkExactHeight()
    {
        if (!empty($this->exactHeight)) {
            return $this->height == $this->exactHeight;
        }
        return true;
    }

    public function checkMaxWidth()
    {
        if (!empty($this->maxWidth)) {
            return $this->width <= $this->maxWidth;
        }
        return true;
    }

    public function checkMinWidth()
    {
        if (!empty($this->minWidth)) {
            return $this->width <= $this->minWidth;
        }
        return true;
    }

    public function checkMaxHeight()
    {
        if (!empty($this->maxHeight)) {
            return $this->height <= $this->maxHeight;
        }
        return true;
    }

    public function checkMinHeight()
    {
        if (!empty($this->minHeight)) {
            return $this->height <= $this->minHeight;
        }
        return true;
    }
}