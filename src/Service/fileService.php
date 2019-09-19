<?php

namespace UploadFiles\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class fileService implements fileServiceInterface {

    protected $config;
    protected $mimeType;
    protected $fileName;
    protected $tempFileName;
    protected $errorNr;
    protected $fileSize;

    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Funtion to upload files
     * @param array $file file array of a post
     * @param boolean $allowCopy fals or true. Set to true when overiding the file with same name
     * @param string $settings settings to use to upload the file
     *
     * @return string or array
     */
    public function uploadFile($file, $allowCopy = false, $settings = 'default') {

        //Set settings in variables
        $destinationFolder = $this->config['filesUploadSettings'][$settings]['uploadFolder'];
        $maxFileSize = (int) $this->config['filesUploadSettings'][$settings]['uploadeFileSize'];
        $allowedExtensions = $this->config['filesUploadSettings'][$settings]['allowedFileExtensions'];

        //Put file array in variables
        $this->mimeType = $file['type']; //file type
        $this->fileName = $file['name']; //file name
        $this->tempFileName = $file['tmp_name']; //temporary file name
        $this->errorNr = $file['error']; //error number
        $this->fileSize = $file['size']; //file size
        //Check if there is a error in the file
        if ($this->errorNr <> 0) {
            return "Return Code: " . $this->errorNr . "<br>";
        }
        //Check if the mime type of the file is allowed by the settings array
        if ($allowedExtensions != NULL) {
            //Check if the file is allowed
            if (!in_array($this->mimeType, $allowedExtensions)) {
                return 'File extension not allowed';
            }
        }
        // Check if the directory exist
        if (!is_dir($destinationFolder)) {
            return 'Folder ' . $destinationFolder . ' does not exist';
        }
        // Check if the directory has the appropiate rights
        if (substr(sprintf('%o', fileperms($destinationFolder)), -4) <> '0777') {
            return 'The folder does not has the appropirate rights to upload files.';
        }
        // Check is the file size is not to big
        if ($this->fileSize > $maxFileSize) {
            return 'The file size is to big.';
        }
        //Convert file name to smal and friendly letters
        $this->fileName = $this->friendlyFileURL($this->fileName);
        //Set the file folder
        $this->pathToFile = $destinationFolder . "/" . $this->fileName;
        //Get the path parts and set them into a array
        $pathParts = pathinfo($this->pathToFile);
        //Check if allow copy is false. When false the file will be uploaded with a unique name
        if ($allowCopy === false) {
            # make unique filename
            $addition = 1; //counter for making unique addition
            $uniqueFileName = ''; //addition for making filename unique
            while (file_exists($destinationFolder . "/" . $pathParts['filename'] . $uniqueFileName . '.' . $pathParts['extension'])) {
                $uniqueFileName = '(' . $addition . ')';
                $addition++;
            }
        }
        //Create filename
        $this->fileName = $pathParts['filename'] . $uniqueFileName . '.' . $pathParts['extension'];
        //Create path (including file name)
        $this->pathToFile = $destinationFolder . "/" . $this->fileName;
        //Upload file to server
        move_uploaded_file($this->tempFileName, $this->pathToFile);
        //Return array
        return [
            'path' => $this->pathToFile,
            'name' => $pathParts['filename'],
            'type' => $pathParts['filename']
        ];
    }

    /**
     * Return a friendly url
     * @param string $string
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

    /*
     * When having a excisting form you can add a file input
     */
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

    /**
     * Remove file from server
     * @param $path string path to file
     *
     * @return boolean
     */
    public function removeFile($path)
    {
        if (file_exists($path)) {
            @unlink($path);
            return true;
        } else {
            return false;
        }
    }

}
