<?php

namespace UploadFiles\Service;

interface uploadfilesServiceInterface {

    public function uploadFile($aFile, $bAllowCopy = false);
    
    public function friendlyFileURL($string);
    
    public function addFileInputToForm($form, $inputName = 'fileUpload', $labelName = 'File');
    
}
