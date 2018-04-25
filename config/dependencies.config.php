<?php

namespace Zfe\Common;

use Zfe\Common\Factory\LoggingErrorListenerFactory;
use Zfe\Common\Factory\AbstractProviderFactory;
use Zend\Stratigility\Middleware\ErrorHandler;

return [
    'dependencies' => [
        'invokables' => [
        ],
        'factories'  => [
        ],
        'delegators' => [
            ErrorHandler::class => [
                LoggingErrorListenerFactory::class,
            ],
        ],
        'abstract_factories' => [
            AbstractProviderFactory::class
        ]
    ]
];
