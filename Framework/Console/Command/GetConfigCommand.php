<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 15-5-2020 16:31
 */

namespace Henri\Framework\Console\Command;

use Henri\Framework\Configuration\Configuration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class GetConfigCommand extends Command {

	/**
	 * @var Configuration $configuration
	 */
	private $configuration;

	/**
	 * the name of the command (the part after "bin/henri")
	 * @var string $defaultName
	 */
	protected static $defaultName = 'config:get';

	/**
	 * GetConfigCommand constructor.
	 *
	 * @param Configuration $configuration
	 */
	public function __construct(
		Configuration   $configuration
	)
	{
		$this->configuration    = $configuration;

		parent::__construct();
	}

	/**
	 * Method to set command configuration
	 */
	protected function configure() {
		$this
			// the short description shown while running "php bin/console list"
			->setDescription('Get a setting value')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command will get setting values.')

			// configure an argument
			->addArgument('name', InputArgument::REQUIRED, 'The name of the setting')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$value = $this->configuration->get($input->getArgument('name'));
		if ($value) {
			$output->writeln('Current value for ' . $input->getArgument('name') . ': ' . $value);
		} else {
			$output->writeln('Setting ' . $input->getArgument('name') . ' is not found or empty');
		}

		return 0;
	}

}