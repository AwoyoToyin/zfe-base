<?php

namespace Zfe\Common\Factory;

use Psr\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Log\Processor\PsrPlaceholder;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zfe\Common\Pipeline\LoggingErrorListener;

class LoggingErrorListenerFactory
{
    public function __invoke(
        ContainerInterface $container,
        $serviceName,
        callable $callback
    ) : ErrorHandler {
        $logger = $container->get(Logger::class);
        $logger->addProcessor(new PsrPlaceholder());

        // Writer
        $writer = new Writer\Stream($container->get('config')['logger']['filename']);
        $logger->addWriter($writer);

        $format = $container->get('config')['logger']['format'];
        $listener = new LoggingErrorListener($logger, $format);

        $errorHandler = $callback();
        $errorHandler->attachListener($listener);
        return $errorHandler;
    }
}
