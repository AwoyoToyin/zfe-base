<?php

namespace Zfe\Common;

use Common\Factory\LoggingErrorListenerFactory;
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
