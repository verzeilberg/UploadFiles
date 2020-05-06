<?php

namespace UploadFiles;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'controllers' => [
        'factories' => [
            Controller\UploadFilesController::class => Factory\UploadFilesControllerFactory::class,
        ],
        'aliases' => [
            'uploadfilesbeheer' => Controller\UploadFilesController::class,
        ],
    ],
    'service_manager' => [
        'invokables' => [
            'UploadFiles\Service\uploadfilesServiceInterface' => 'UploadFiles\Service\uploadfilesService'
        ],
    ],
    // The following section is new and should be added to your file
    'router' => [
        'routes' => [
            'uploadfiles' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/uploadfiles[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'uploadfilesbeheer',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'uploadfiles' => __DIR__ . '/../view',
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            'uploadfilesbeheer' => [
                // to anyone.
                ['actions' => '*', 'allow' => '+uploadfiles.manage']
            ],
        ]
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
    'filesUploadSettings' => [
        'default' => [
            'uploadFolder' => '',
            'uploadeFileSize' => '',
            'allowedFileExtensions' => [],
        ]
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../public',
            ],
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],
];
