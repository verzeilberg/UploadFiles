<?php

namespace UploadFiles\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UploadFiles\Controller\UploadFilesController;
use UploadFiles\Service\uploadfilesService;

/**
 * This is the factory for the UploadFilesController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class UploadFilesControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $vhm = $container->get('ViewHelperManager');
        $uploadfilesService = new uploadfilesService($entityManager);
        return new UploadFilesController($vhm, $entityManager, $uploadfilesService);
    }

}
