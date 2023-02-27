<?php

namespace UploadFiles\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Symfony\Component\VarDumper\VarDumper;

class UploadFilesAjaxController extends AbstractActionController {

    protected $vhm;
    protected $em;
    protected $uploadfilesService;

    public function __construct($vhm, $em, $uploadfilesService) {
        $this->vhm = $vhm;
        $this->em = $em;
        $this->uploadfilesService = $uploadfilesService;
    }

    public function addAction() {

        $file = $this->getRequest()->getFiles('filelink');
        $result = $this->uploadfilesService->uploadFile($file);

        return $result;

    }
}
