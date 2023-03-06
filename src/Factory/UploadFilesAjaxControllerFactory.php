<?php

namespace UploadFiles\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UploadFiles\Controller\UploadFilesAjaxController;
use UploadFiles\Controller\UploadFilesController;
use UploadFiles\Service\uploadfilesService;

/**
 * This is the factory for the UploadFilesController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class UploadFilesAjaxControllerFactory implements FactoryInterface {

    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): UploadFilesAjaxController
    {
        
        $entityManager      = $container->get('doctrine.entitymanager.orm_default');
        $vhm                = $container->get('ViewHelperManager');
        $config             = $container->get('config');
        $uploadFilesService = new uploadfilesService($config);
        return new UploadFilesAjaxController(
            $vhm,
            $entityManager,
            $uploadFilesService
        );
    }

}
