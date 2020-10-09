<?php

/**
 * Image
 */
class Image
{
    private $image;
    private $url;

    function __construct($url)
    {
        $this->url = $url;

        $this->create();
    }

    function create()
    {
        $imgParts = pathinfo($this->url);
        $extension = $imgParts['extension'];

        // Load image file
        switch ($extension) {
            case 'jpg':
                $this->image = imagecreatefromjpeg($this->url);
                break;
            case 'png':
                $this->image = imagecreatefrompng($this->url);
                break;
            case 'bmp':
                $this->image = imagecreatefrombmp($this->url);
                break;
            default:
                die('Wrong image extentation!');
                break;
        }
    }

    public function scaleImage($maxWidth, $maxHeight)
    {
        $width = imagesx($this->image);
        $height = imagesy($this->image);

        $hRatio = $height / $maxHeight;
        $wRatio = $width / $maxWidth;

        if ($hRatio > $wRatio) {
            $newWidth = $width / $hRatio;
            $newHeight = $maxHeight;
        } else {
            $newWidth = $maxWidth;
            $newHeight = $height / $wRatio;
        }

        $this->image = imagescale($this->image, $newWidth, $newHeight);
    }

    public function getBase64File()
    {
        $imgParts = pathinfo($this->url);
        $extension = $imgParts['extension'];

        ob_start();

        switch ($extension) {
            case 'jpg':
                imagejpeg($this->image);
                break;
            case 'png':
                imagepng($this->image);
                break;
            case 'bmp':
                imagebmp($this->image);
                break;
            default:
                die('Wrong image extentation!');
                break;
        }

        $base64File = ob_get_clean();
        return $base64File;
    }

    public function saveToServer($fileName)
    {
        file_put_contents($fileName, $this->getBase64File());
    }

    /**
     * Get the value of image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @return  self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the value of url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @return  self
     */
    public function setUrl($url)
    {
        $this->url = $url;
        $this->create();

        return $this;
    }
}
