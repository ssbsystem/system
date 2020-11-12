<?php

/**
 * Remove image
 */
class RemoveImage
{
    private $imgUrl;

    function __construct($imgUrl)
    {
        $this->imgUrl = $imgUrl;
    }

    public function Remove()
    {
        unlink($this->imgUrl);
        unlink(str_replace('img/desc', 'img/mob', $this->imgUrl));

        return 'Success';
    }
}
