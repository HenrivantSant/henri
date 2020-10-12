<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 13-12-2019 17:05
 */

namespace Henri\Framework\Database;

use Dibi\Connection;
use Henri\Framework\Configuration\Configuration;

class DatabaseDriver extends Connection
{
	/**
	 * @var Configuration $configuation
	 */
	protected $configuation;

	/**
	 * @var string $prefix  table prefix
	 */
	protected $prefix;

	/**
	 * HenriDatabaseDriver constructor.
	 * @throws \Dibi\Exception
	 */
	public function __construct(
		Configuration $configuration
	) {
		$this->configuation = $configuration;
		$this->prefix       = $configuration->get('database.prefix');
		$config = array(
				'driver'    => $configuration->get('database.driver'),
				'host'      => $configuration->get('database.host'),
				'username'  => $configuration->get('database.username'),
				'password'  => $configuration->get('database.password'),
				'database'  => $configuration->get('database.database'),
				'port'      => intval($configuration->get('database.port')),
		);

		parent::__construct($config);
	}

	/**
	 * Method to get table prefix
	 *
	 * @return string
	 */
	public function getPrefix() : string {
		return $this->prefix;
	}
}