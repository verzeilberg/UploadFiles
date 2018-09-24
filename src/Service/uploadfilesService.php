<?php

namespace UploadFiles\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

/*
 * Entities
 */
use UploadFiles\Entity\UploadFiles;

class uploadfilesService implements uploadfilesServiceInterface {

    protected $config;
    protected $mimeType;
    protected $sFileName;
    protected $sTempFileName;
    protected $iErrorNr;
    protected $iFileSize;
    protected $entityManager;

    /**
     * Constructor.
     */
    public function __construct($config, $entityManager) {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function uploadFile($aFile, $bAllowCopy = false) {
        
       
        
        $sDestinationFolder = 'public/' . $this->config['filesUploadSettings']['uploadFolder'];
        $filePath = $this->config['filesUploadSettings']['uploadFolder'];
        $iMaxFileSize = (int) $this->config['filesUploadSettings']['uploadeFileSize'];
        $aAllowedExtensions = array();
        $aAllowedExtensions = $this->config['filesUploadSettings']['allowedFileExtensions'];


        //Put file array in variables
        $this->mimeType = $aFile['type']; //file type
        $this->sFileName = $aFile['name']; //file name
        $this->sTempFileName = $aFile['tmp_name']; //temporary file name
        $this->iErrorNr = $aFile['error']; //error number
        $this->iFileSize = $aFile['size']; //file size
        //Check if there is a error in the file
        
        if ($this->iErrorNr !== 0) {
            return "Return Code: " . $this->iErrorNr . "<br>";
        }
        
        

        if ($aAllowedExtensions != NULL) {
            //Check if the file is allowed by the module
            if (!in_array($this->mimeType, $aAllowedExtensions)) {
                return 'File extension not allowed';
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
        
        $sUnique = ''; //addition for making filename unique
        if ($bAllowCopy === false) {
            # make unique filename
            $iT = 1; //counter for making unique addition
            
            while (file_exists($sDestinationFolder . "/" . $sPathParts['filename'] . $sUnique . '.' . $sPathParts['extension'])) {
                $sUnique = '(' . $iT . ')';
                $iT++;
            }
        }

        $this->sFileName = $sPathParts['filename'] . $sUnique . '.' . $sPathParts['extension'];

        $this->sPathToFile = $sDestinationFolder . "/" . $this->sFileName;

        if(move_uploaded_file($this->sTempFileName, $this->sPathToFile)) 
        {
            $file = [];
            $file['name'] = $this->sFileName;
            $file['type'] = $this->mimeType;
            $file['size'] = $this->iFileSize;
            $file['path'] = $filePath . "/" . $this->sFileName;;
            
            return $file;
        } else {
            return 'File not  uploaded';
        }
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
                'class' => 'form-control-file',
            ],
        ]);
        
        $form->add([
            'name' => 'fileDescription',
            'type' => 'text',
            'options' => array(
                'label' => 'File description'
            ),
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        return $form;
    }

    /**
     *
     * Create file object
     *
     * @return   object
     *
     */
    public function createFile() {
        return new UploadFiles();
    }

    /**
     *
     * Create file object
     *
     * @return   object
     *
     */
    public function setNewFile($file, $data, $description = null, $currentUser) {
        $file->setName($data['name']);
        $file->setType($data['type']);
        $file->setSize($data['size']);
        $file->setPath($data['path']);
        $file->setDescription($description);
        $file->setDateCreated(new \DateTime());
        $file->setCreatedBy($currentUser);
        
        $this->storeFile($file);
        
    }
    
        /**
     *
     * Set data to existing event
     *
     * @param       event $event object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setExistingFile($file, $currentUser) {
        $eventCategory->setDateChanged(new \DateTime());
        $eventCategory->setChangedBy($currentUser);
        $this->storeEventCategory($eventCategory);
    }
    
        /**
     *
     * Save blog to database
     *
     * @param       blog object
     * @return      void
     *
     */
    public function storeFile($file) {
        $this->entityManager->persist($file);
        $this->entityManager->flush();
    }
    
        /**
     *
     * Delete a Event object from database
     * @param       event $event object
     * @return      object
     *
     */
    public function deleteFile($file) {
        
        @unlink('public/' . $file->getPath());
        
        $this->entityManager->remove($file);
        $this->entityManager->flush();
    }
    

}
