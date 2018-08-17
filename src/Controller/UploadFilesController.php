<?php

namespace UploadFiles\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UploadFilesController extends AbstractActionController {

    protected $vhm;
    protected $em;
    protected $uploadfilesService;

    public function __construct($vhm, $em, $uploadfilesService) {
        $this->vhm = $vhm;
        $this->em = $em;
        $this->uploadfilesService = $uploadfilesService;
    }

    public function indexAction() {
        $this->layout('layout/beheer');
        $uploadfilesForms = $this->uploadfilesService->getUploadFiless();
        return new ViewModel(
                array(
            'uploadfilesForms' => $uploadfilesForms,
                )
        );
    }

    /**
     * 
     * Action to set delete a blog
     */
    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/uploadfiles');
        }
        $uploadfilesForm = $this->uploadfilesService->getUploadFilesFormById($id);
        if (empty($uploadfilesForm)) {
            return $this->redirect()->toRoute('beheer/uploadfiles');
        }
        // Remove blog
        $this->uploadfilesService->deleteUploadFilesForm($uploadfilesForm);
        $this->flashMessenger()->addSuccessMessage('UploadFiles verwijderd');
        return $this->redirect()->toRoute('beheer/uploadfiles');
    }
}
