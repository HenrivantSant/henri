<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 15-12-2019 16:02
 */

namespace Henri\Framework\Model\Entity;

use Henri\Framework\Database\DatabaseDriver;

class EntityManagerSingle extends EntityManager
{

	/**
	 * EntityManager constructor.
	 *
	 * @param DatabaseDriver $databaseDriver
	 */
	public function __construct(
		DatabaseDriver $databaseDriver
	) {
		parent::__construct($databaseDriver);
	}

	/**
	 * Method to load data for entities by given keys
	 *
	 * @param array $keys     defaults to all when empty
	 * @param bool  $autoload whether to autoload from state
	 *
	 * @return bool true on loaded, false on not loaded
	 */
	public function load(array $keys = array(), bool $autoload = false) : bool {
		if (!empty($keys)) {
			$this->validateQueryKeys($keys);
		}

		$mainEntityPrimaryKey = $this->entities[$this->mainEntity]->get('primaryKey');

		$query = $this->database->select('*');
		$query->from('[' . $this->entities[$this->mainEntity]->getTableName() . ']')->as($this->mainEntity);

		if (!empty($this->mapping)) {
			foreach ($this->mapping as $key => $map) {
				// Loop through entities and add their states to where clause
				$query->innerJoin('[' . $this->entities[$key]->getTableName() . ']')->as($key)->on('[' . $this->mainEntity . '.' . $map['mainKey'] . '] =', '[' . $key . '.' . $map['joinKey'] . ']');
			}
		}

		if (!$autoload) {
			// Where clause on primary key
			$query->where('[' . $this->mainEntity . '.' . $mainEntityPrimaryKey . '] = ' . $this->entities[$this->mainEntity]->get($mainEntityPrimaryKey));
		} else {
			foreach ($this->entities as $entityAlias => $entity) {
				// Set all entity values as where clause
				$values = $this->entities[$entityAlias]->getValuesAsArray(true); // true = perform onBeforeSave actions

				if (empty($values)) {
					continue;
				}

				foreach ($values as $propertyName => $value) {
					if (is_null($value)) {
						continue;
					}
					$where = $this->entities[$entityAlias]->getPropertyWhereClause($propertyName);
					$query->where($where['prepare'], $where['value']);
				}
			}
		}

		$result = $query->fetch()->toArray();

		if (!is_array($result) || empty($result)) {
			return false;
		}

		foreach ($this->entities as $entity) {
			$entity->populateStateFromDB($result);
		}

		return true;
	}

}