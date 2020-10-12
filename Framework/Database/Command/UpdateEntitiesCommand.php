<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 15-5-2020 16:31
 */

namespace Henri\Framework\Database\Command;

use Henri\Framework\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use \Henri\Framework\ContainerService\ContainerService;


class UpdateEntitiesCommand extends Command {

	/**
	 * @var ContainerService $containerService
	 */
	private $containerService;

	/**
	 * the name of the command (the part after "bin/henri")
	 * @var string $defaultName
	 */
	protected static $defaultName = 'database:entities:update';


	/**
	 * GetClientCommand constructor.
	 *
	 */
	public function __construct() {
		global $containerBuilder;
		$this->containerService = $containerBuilder;

		parent::__construct();
	}

	/**
	 * Method to set command configuration
	 */
	protected function configure() {
		$this
			// the short description shown while running "php bin/console list"
			->setDescription('Update all tables by their entities')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command will update tables from their respective entity')

			// configure an argument
			->addArgument('remove_non_existing_columns', InputArgument::OPTIONAL, 'Remove database columns which are not represented in the given entity. Do this by adding the flag remove_non_existing to this command.')

			->addArgument('drop_table_if_exists', InputArgument::OPTIONAL, 'Drop the tables if they exist. Do this by adding the flag drop_table to this command.')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$removeNonExistingColumns   = $input->getArgument('remove_non_existing_columns');
		$removeNonExistingColumns   = strtolower($removeNonExistingColumns) === 'remove_non_existing';
		$dropTableIfExists          = !is_null($input->getArgument('drop_table_if_exists')) && strtolower($input->getArgument('drop_table_if_exists')) === 'drop_table';

		$entities   = $this->containerService->getDefinitionsByTag('Entity');

		foreach ($entities as $entity) {
			try {
				$this->updateEntity($input, $output, $entity, $removeNonExistingColumns, $dropTableIfExists);
			} catch (\Exception $exception) {
				$output->writeln($exception->getMessage());
			}
		}


		return 0;
	}

	private function updateEntity(InputInterface $input, OutputInterface $output, string $entityName, bool $removeNonExistingColumns, bool $dropTableIfExists) {
		if (!$this->containerService->has($entityName)) {
			$output->writeln('Entity ' . $entityName . ' is not found. Is it registered in the Container?');
			return 0;
		}

		$entity = $this->containerService->get($entityName);
		if (!is_subclass_of($entity, 'Henri\Framework\Model\Entity\Entity')) {
			//$output->writeln($entityName . ' is not a valid entity');
			return 0;
		}

		try {
			$output->writeln('Updating table ' . $entity->getTableName() . ' for entity ' . $entityName);
			$nonExistingColumns = $entity->updateTable($removeNonExistingColumns, $dropTableIfExists);
			$output->writeln('Updated ' . $entity->getTableName() . ' successfully');

			if (!empty($nonExistingColumns) && !$removeNonExistingColumns) {
				$nonExistingColumns = join(', ', $nonExistingColumns);
				$output->writeln('The following columns are found in the table, but not represented as properties: ' . $nonExistingColumns . '. Remove them or add them as a property to the entity. You can easily remove them by adding the remove_non_existing flag to this command');
			} elseif (!empty($nonExistingColumns) && $removeNonExistingColumns) {
				$nonExistingColumns = join(', ', $nonExistingColumns);
				$output->writeln('The following non property represented columns were found and removed from the table: ' . $nonExistingColumns);
			}

		} catch (\Exception $exception) {
			$output->writeln($exception->getMessage());
		}
	}
}