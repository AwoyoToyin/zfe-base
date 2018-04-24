<?php

namespace Zfe\Common;

use Zend\ConfigAggregator\PhpFileProvider;
use Zend\ConfigAggregator\ConfigAggregator;

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
        /** @var array $config */
        $aggregator = new ConfigAggregator([
            new PhpFileProvider(realpath(__DIR__) . '/../config/{,*.}config.php')
        ]);
        $config = $aggregator->getMergedConfig();
        return $config;
    }
}
