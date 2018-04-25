<?php
/**
 * Description of AbstractProviderFactory
 *
 * @author: Awoyo Oluwatoyin Stephen alias awoyotoyin <awoyotoyin@gmail.com>
 */
namespace Zfe\Common\Factory;

use Interop\Container\ContainerInterface;
use ReflectionClass;
use Zend\EventManager\EventManager;
use Zend\EventManager\LazyListener;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zfe\Common\Provider\ProviderInterface;

class AbstractProviderFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Construct a new ReflectionClass object for the requested action
        $reflection = new ReflectionClass($requestedName);

        // Get the constructor
        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            // There is no constructor, just return a new class
            return new $requestedName;
        }

        $dependencies = $this->injectDependencies($container, $constructor);

        // Return the requested class and inject its dependencies
        /** ProviderInterface @var $instance */
        $instance = $reflection->newInstanceArgs($dependencies);

        $this->registerEventListeners($container, $instance);

        return $instance;
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        // Only accept Provider classes
        if (substr($requestedName, -8) == 'Provider') {
            return true;
        }

        return false;
    }

    protected function injectDependencies(ContainerInterface $container, $constructor)
    {
        // Get the parameters
        $parameters = $constructor->getParameters();

        $dependencies = [];

        foreach ($parameters as $parameter) {
            // Get the parameter class
            $class = $parameter->getClass();
            $className = $class->getName();

            if ($className === 'Doctrine\ORM\EntityManagerInterface') {
                $className = 'doctrine.entity_manager.orm_default';
            }

            // Get the class from the container
            $dependencies[] = $container->get($className);
        }

        return $dependencies;
    }

    protected function registerEventListeners(ContainerInterface $container, ProviderInterface $instance)
    {
        $events = $container->get('config')['listeners']['events'];

        foreach ($events as $key => $event) {
            if (!isset($event['class']) || !isset($event['method'])) {
                continue;
            }

            $lazyListener = new LazyListener([
                'listener'  => $event['class'],
                'method'    => $event['method']
            ], $container);

            $instance->getEventManager()->attach($key, $lazyListener);
        }
    }
}
