<?php

namespace Zfe\Common;

use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\ServiceManager\Factory\InvokableFactory;
use Common\Factory\LoggingErrorListenerFactory;
use Zend\Stratigility\Middleware\ErrorHandler;
use Doctrine\Common\Cache\Cache;

/**
 * The configuration provider for the Common module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies()
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [
            ],
            'factories'  => [
            ],
            'delegators' => [
                ErrorHandler::class => [
                    LoggingErrorListenerFactory::class,
                ],
            ],
        ];
    }
}
