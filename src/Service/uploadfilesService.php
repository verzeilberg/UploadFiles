<?php

namespace UploadFiles\Service;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Symfony\Component\VarDumper\VarDumper;
use UploadFiles\Exception\fileException;

class uploadfilesService {

    protected $config;
    protected $mimeType;
    protected $fileName;
    protected $tempFileName;
    protected $errorNr;
    protected $fileSize;
    protected $documentRoot;

    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Function to upload files
     * @param array $file file array of a post
     * @param boolean $allowCopy false or true. Set to true when overriding the file with same name
     * @param string $fileUploadSettingsKey
     * @return string or array
     * @throws fileException
     */
    public function uploadFile(
        array $file,
        bool $allowCopy = false,
        string $fileUploadSettingsKey = 'default'
    ) {
        //Put file array in variables
        $this->mimeType = $file['type']; //file type
        $this->fileName = $file['name']; //file name
        $this->tempFileName = $file['tmp_name']; //temporary file name
        $this->errorNr = $file['error']; //error number
        $this->fileSize = $file['size']; //file size
        $this->documentRoot = $_SERVER['DOCUMENT_ROOT'];
        //Check if there is an error in the file
        if ($this->errorNr !== 0) {
            return new fileException('Error uploading the file: ' . $this->errorNr);
        }

        /** Set fileUploadSettings from config in variable */
        $fileUploadSettings = $this->config['filesUploadSettings'];

        /**
         * Check if uploadsettings excists in config array
         * @param $imageUploadSettings string
         * @param $imageUploadSettings
         * @return void
         */
        if (!array_key_exists($fileUploadSettingsKey, $fileUploadSettings)) {
            throw new fileException(sprintf('Given settings, %s, does not exists in the config file', $fileUploadSettingsKey));
        }

        //Set settings in variables
        $destinationFolder = $this->documentRoot . $this->config['filesUploadSettings'][$fileUploadSettingsKey]['uploadFolder'];
        $maxFileSize = (int) $this->config['filesUploadSettings'][$fileUploadSettingsKey]['uploadeFileSize'];
        $allowedExtensions = $this->config['filesUploadSettings'][$fileUploadSettingsKey]['allowedFileExtensions'];

        //Check if the mime type of the file is allowed by the settings array
        //Check if the file is allowed
        if ((!empty($allowedExtensions)) && !in_array($this->mimeType, $allowedExtensions, true)) {
            throw new fileException( 'File extension not allowed');
        }
        // Check if the directory exist
        if (!is_dir($destinationFolder)) {
           $result = mkdir($destinationFolder, 0777, true);
        } elseif (!is_writable($destinationFolder)) {
            chmod($destinationFolder, 0777);
        }

        // Check is the file size is not to big
        if ($this->fileSize > $maxFileSize) {
            throw new fileException( 'The file size is to big.');
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
        $this->pathToFile = $destinationFolder . $this->fileName;
        //Upload file to server
        $result = move_uploaded_file($this->tempFileName, $this->pathToFile);
        //Return array
        return [
            'path' => $this->pathToFile,
            'name' => $pathParts['filename'],
            'type' => $pathParts['extension'],
            'result' => $result,
        ];
    }

    /**
     * Return a friendly url
     * @param string $string
     * @return String
     */
    public function friendlyFileURL(string $string): string
    {
        // First replace special characters voor plain letters
        $pattern = array("'Ã©'", "'Ã¨'", "'Ã«'", "'Ãª'", "'Ã‰'", "'Ãˆ'", "'Ã‹'", "'ÃŠ'", "'Ã¡'", "'Ã '", "'Ã¤'", "'Ã¢'", "'Ã¥'", "'Ã?'", "'Ã€'", "'Ã„'", "'Ã‚'", "'Ã…'", "'Ã³'", "'Ã²'", "'Ã¶'", "'Ã´'", "'Ã“'", "'Ã’'", "'Ã–'", "'Ã”'", "'Ã­'", "'Ã¬'", "'Ã¯'", "'Ã®'", "'Ã?'", "'ÃŒ'", "'Ã?'", "'ÃŽ'", "'Ãº'", "'Ã¹'", "'Ã¼'", "'Ã»'", "'Ãš'", "'Ã™'", "'Ãœ'", "'Ã›'", "'Ã½'", "'Ã¿'", "'Ã?'", "'Ã¸'", "'Ã˜'", "'Å“'", "'Å’'", "'Ã†'", "'Ã§'", "'Ã‡'");
        $replace = array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'i', 'i', 'i', 'I', 'I', 'I', 'I', 'I', 'u', 'u', 'u', 'u', 'U', 'U', 'U', 'U', 'y', 'y', 'Y', 'o', 'O', 'a', 'A', 'A', 'c', 'C');
        $string = preg_replace($pattern, $replace, $string);
        // Second replace all special characters but keep dot, space and underscore
        $string = preg_replace("/[^a-zA-Z0-9. _-]/", "", $string);
        // Third replace al spaces with dashes
        $string = preg_replace('/\s+/', '-', $string);
        // Fourth transfer all characters to lower string
        return strtolower($string);
    }

    /**
     * When having an existing form you can add a file input
     * @param $form
     * @param string $inputName
     * @param string $labelName
     * @return mixed
     */
    public function addFileInputToForm(
        $form,
        string $inputName = 'fileUpload',
        string $labelName = 'File'
    ) {
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
     * @return boolean
     */
    public function removeFile(string $path): bool
    {
        if (file_exists($path)) {
            @unlink($path);
            return true;
        }

        return false;
    }

}
