<?php

namespace Zfe\Common\Pipeline;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Zend\Log\Logger;

class LoggingErrorListener
{
    /**
     * Log message string with placeholders
     */
    const LOG_STRING = '{status} [{method}] {uri}: {error}';

    /**
     * @var Logger
     */
    private $logger;

    /**
     * The Log format
     *
     * @var string
     */
    private $format;

    public function __construct(Logger $logger, string $format)
    {
        $this->logger = $logger;
        $this->format = $format;
    }

    public function __invoke(
        $error,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->logger->err($this->format | self::LOG_STRING, [
            'status' => $response->getStatusCode(),
            'method' => $request->getMethod(),
            'uri'    => (string) $request->getUri(),
            'error'  => $error->getMessage(),
        ]);
    }
}
