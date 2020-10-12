<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 14-12-2019 11:25
 */

namespace Henri\Framework\ContainerService;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ContainerService extends ContainerBuilder {

	/**
	 * Compiles the container.
	 *
	 * This method passes the container to compiler
	 * passes whose job is to manipulate and optimize
	 * the container.
	 *
	 * The main compiler passes roughly do four things:
	 *
	 *  * The extension configurations are merged;
	 *  * Parameter values are resolved;
	 *  * The parameter bag is frozen;
	 *  * Extension loading is disabled.
	 *
	 * @param bool $resolveEnvPlaceholders Whether %env()% parameters should be resolved using the current
	 *                                     env vars or be replaced by uniquely identifiable placeholders.
	 *                                     Set to "true" when you want to use the current ContainerBuilder
	 *                                     directly, keep to "false" when the container is dumped instead.
	 */
	public function compile(bool $resolveEnvPlaceholders = false)
	{
		// Add namespace as tags to the definition
		foreach ($this->getDefinitions() as $key => $definition) {
			$tags = explode('\\', strtolower($key));
			if(is_array($tags) && !empty($tags)) {
				foreach ($tags as $tag) {
					$definition->addTag($tag);
				}
			}
		}

		// Register events to the container
		$this->registerEvents();

		parent::compile();
	}

	/**
	 * Method to get classes by tag
	 *
	 * @param string $tag
	 *
	 * @return array
	 */
	public function getDefinitionsByTag(string $tag) : array {
		$definitions = array();

		if (empty($this->getDefinitions())) {
			return $definitions;
		}

		$tag = strtolower($tag);
		foreach ($this->getDefinitions() as $key => $definition) {
			if ($definition->hasTag($tag)) {
				array_push($definitions, $key);
			}
		}

		return $definitions;
	}

    /**
     * Register events to the container
     *
     * @throws \ReflectionException
     */
    private function registerEvents(): void {
        // Register event dispatcher for dependency injection (that's why it's set to public)
        $this->register(EventDispatcher::class, EventDispatcher::class)->setPublic(true);

        // Get all event subscribers and register them by name
        foreach ($this->getDefinitionsByTag('EventSubscriber') as $eventSubscriber) {
            $this->register($eventSubscriber, $eventSubscriber)->addTag('kernel.event_subscriber');
            $this->get(EventDispatcher::class)->addSubscriber($this->get($eventSubscriber));
        }
	}
}