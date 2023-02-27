<?php

namespace UploadFiles\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Symfony\Component\VarDumper\VarDumper;
use UploadFiles\Exception\fileException;

class UploadFilesAjaxController extends AbstractActionController {

    protected $vhm;
    protected $em;
    protected $uploadfilesService;

    public function __construct($vhm, $em, $uploadfilesService) {
        $this->vhm = $vhm;
        $this->em = $em;
        $this->uploadfilesService = $uploadfilesService;
    }

    /**
     * @return array
     */
    public function addAction(): array
    {

        $file = $this->getRequest()->getFiles('filelink');
        try {
            $result =  $this->uploadfilesService->uploadFile($file, true);

            return [
                'message' => 'File succesfull uploaded',
                'success' => true,
                'file' => $result,
            ];
        } catch (fileException $exception) {
            return [
                'message' => $exception->customMessage(),
                'success' => false,
            ];
        }
    }
}
