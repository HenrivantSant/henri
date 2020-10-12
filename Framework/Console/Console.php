<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 15-5-2020 15:50
 */

namespace Henri\Framework\Console;

use Henri\Framework\Kernel\ContainerService\ContainerService;

class Console {

	/**
	 * @var Application $application
	 */
	private $application;

	/**
	 * @var ContainerService    $containerService
	 */
	private $containerService;

	/**
	 * Console constructor.
	 *
	 * @param Application $application
	 */
	public function __construct(
		Application $application
	) {
		global $containerBuilder;

		$this->application      = $application;
		$this->containerService = $containerBuilder;
	}

	/**
	 * Method to run command line
	 *
	 * @throws \Exception
	 */
	public function run() : void {
		// Get all the commands
		$this->registerCommands();

		// Run application
		$this->application->run();
	}

	/**
	 * Method to register commands
	 *
	 * @throws \Exception
	 */
	private function registerCommands() : void {
		$commands       = array();
		$commandClasses = $this->containerService->getDefinitionsByTag('Command');

		foreach ($commandClasses as $commandClass) {
			array_push($commands, $this->containerService->get($commandClass));
		}

		if (!empty($commands)) {
			$this->application->addCommands($commands);
		}
	}


}