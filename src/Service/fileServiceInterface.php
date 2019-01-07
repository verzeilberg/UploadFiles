<?php

namespace UploadFiles\Service;

interface fileServiceInterface {

    /**
     * 
     * @param type $image
     * @return 
     * 
     */
    public function uploadFile($image, $imageUploadSettings = NULL, $imageType = 'original', $Image = NULL, $isOriginal = 0);


}
