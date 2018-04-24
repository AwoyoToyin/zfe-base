<?php

namespace Zfe\Common;

use Zfe\Common\Factory\LoggingErrorListenerFactory;
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
    ]
];
