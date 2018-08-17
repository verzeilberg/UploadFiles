<?php

namespace UploadFiles\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class fileService implements fileServiceInterface {

    protected $config;
    protected $mimeType;
    protected $sFileName;
    protected $sTempFileName;
    protected $iErrorNr;
    protected $iFileSize;

    public function __construct($config) {
        $this->config = $config;
    }

    public function uploadFile($aFile, $bAllowCopy = false) {


        $sDestinationFolder = 'public/' . $this->config['filesUploadSettings']['uploadFolder'];
        $iMaxFileSize = (int) $this->config['filesUploadSettings']['uploadeFileSize'];
        $aAllowedExtensions = array();
        $aAllowedExtensions = $this->config['imageUploadSettings']['allowedFileExtensions'];


        //Put file array in variables
        $this->mimeType = $aFile['type']; //file type
        $this->sFileName = $aFile['name']; //file name
        $this->sTempFileName = $aFile['tmp_name']; //temporary file name
        $this->iErrorNr = $aFile['error']; //error number
        $this->iFileSize = $aFile['size']; //file size
        //Check if there is a error in the file
        if ($this->iErrorNr <> 0) {
            return "Return Code: " . $this->iErrorNr . "<br>";
        }

//
//        //Check if the file is allowed by CMS        
//        if (!array_search($this->sMimeType, MimeTypesExtensions::getAllAllowedMimeTyesExtension($this->sMimeType))) {
//
//            $sStatusMessage = 'File extension not allowed';
//            echo $sStatusMessage;
//            exit;
//        }

        if ($aAllowedExtensions != NULL) {
            //Check if the file is allowed by the module
            if (!in_array($this->sMimeType, $aAllowedExtensions)) {
                return 'File extension not allowed2';
            }
        }

        // Check if the directory exist
        if (!is_dir($sDestinationFolder)) {
            return 'Folder ' . $sDestinationFolder . ' does not exist';
        }

        // Check if the directory has the appropiate rights
        if (substr(sprintf('%o', fileperms($sDestinationFolder)), -4) <> '0777') {
            return 'The folder does not has the appropirate rights to upload files.';
        }

        // Check is the file size is not to big Smaller than 50 mb
        // File size can be set in incl/config.php file
        if ($this->iFileSize > $iMaxFileSize) {
            return 'The file size is to big.';
        }

        //convert file name to smal letters                    
        $this->sFileName = $this->friendlyFileURL($this->sFileName);

        //Set the file folder
        $this->sPathToFile = $sDestinationFolder . "/" . $this->sFileName;

        $sPathParts = pathinfo($this->sPathToFile);

        if ($bAllowCopy === false) {
            # make unique filename
            $iT = 1; //counter for making unique addition
            $sUnique = ''; //addition for making filename unique
            while (file_exists($sDestinationFolder . "/" . $sPathParts['filename'] . $sUnique . '.' . $sPathParts['extension'])) {
                $sUnique = '(' . $iT . ')';
                $iT++;
            }
        }

        $this->sFileName = $sPathParts['filename'] . $sUnique . '.' . $sPathParts['extension'];

        $this->sPathToFile = $sDestinationFolder . "/" . $this->sFileName;

        move_uploaded_file($this->sTempFileName, $this->sPathToFile);


        return 'File uploaded';
    }

    /**
     * Return a friendly url
     * @return String
     */
    public function friendlyFileURL($string) {
        // First replace special characters voor plain letters
        $pattern = array("'Ã©'", "'Ã¨'", "'Ã«'", "'Ãª'", "'Ã‰'", "'Ãˆ'", "'Ã‹'", "'ÃŠ'", "'Ã¡'", "'Ã '", "'Ã¤'", "'Ã¢'", "'Ã¥'", "'Ã?'", "'Ã€'", "'Ã„'", "'Ã‚'", "'Ã…'", "'Ã³'", "'Ã²'", "'Ã¶'", "'Ã´'", "'Ã“'", "'Ã’'", "'Ã–'", "'Ã”'", "'Ã­'", "'Ã¬'", "'Ã¯'", "'Ã®'", "'Ã?'", "'ÃŒ'", "'Ã?'", "'ÃŽ'", "'Ãº'", "'Ã¹'", "'Ã¼'", "'Ã»'", "'Ãš'", "'Ã™'", "'Ãœ'", "'Ã›'", "'Ã½'", "'Ã¿'", "'Ã?'", "'Ã¸'", "'Ã˜'", "'Å“'", "'Å’'", "'Ã†'", "'Ã§'", "'Ã‡'");
        $replace = array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'i', 'i', 'i', 'I', 'I', 'I', 'I', 'I', 'u', 'u', 'u', 'u', 'U', 'U', 'U', 'U', 'y', 'y', 'Y', 'o', 'O', 'a', 'A', 'A', 'c', 'C');
        $string = preg_replace($pattern, $replace, $string);
        // Second replace all special characters but keep dot, space and underscore
        $string = preg_replace("/[^a-zA-Z0-9. _-]/", "", $string);
        // Third replace al spaces with dashes
        $string = preg_replace('/\s+/', '-', $string);
        // Fourth transfer all charcters to lower string
        return strtolower($string);
    }

    public function addFileInputToForm($form, $inputName = 'fileUpload', $labelName = 'File') {
        $form->add([
            'name' => $inputName,
            'type' => 'file',
            'options' => array(
                'label' => $labelName
            ),
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);
        
        return $form;
    }

}
