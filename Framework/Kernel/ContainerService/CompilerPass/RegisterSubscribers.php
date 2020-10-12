<?php


namespace Henri\Framework\Kernel\ContainerService\CompilerPass;


use Henri\Framework\Events\EventDispatcher;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegisterSubscribers implements \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface {

    /**
     * @inheritDoc
     */
    public function process( ContainerBuilder $container ) {

        // Get all event subscribers and register them by name
        foreach ($container->getDefinitionsByTag('EventSubscriber') as $eventSubscriber) {
            $container->get(EventDispatcher::class)->addSubscriber($container->get($eventSubscriber));
        }

        return;

        $eventDispatcher = $container->get(EventDispatcher::class);
        $definition = $container->findDefinition(
            EventDispatcher::class
        );

        foreach ($container->getDefinitions() as $key => $definition) {
            $tags = explode('\\', strtolower($key));
            if(is_array($tags) && !empty($tags)) {
                foreach ($tags as $tag) {
                    if ($tag === 'eventsubscriber') {
                        $definition->addTag('kernel.event_subscriber');
                        $definition->setAutoconfigured(true);
                        $definition->setAutowired(true);
                    }
                }
            }
        }

        foreach ($container->findTaggedServiceIds('kernel.event_subscriber', true) as $id => $attributes) {
            var_dump($id);
            //var_dump($attributes);
            $def = $container->getDefinition($id);
            $class = $def->getClass();
            //var_dump($def);
            //var_dump($class);
            if (!$r = $container->getReflectionClass($class)) {
                throw new InvalidArgumentException(sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
            }
            if (!$r->isSubclassOf(EventSubscriberInterface::class)) {
                throw new InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, EventSubscriberInterface::class));
            }
            $class = $r->name;
            $eventDispatcher->addSubscriber($container->getInstance($id));
            //$args[1] = array(new ServiceClosureArgument(new Reference($id)), $args[1]);
            //$definition->addMethodCall('addListener', $args);
        }
    }
}

