<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 13-12-2019 17:09
 */

namespace Henri\Framework\Model\Entity;

use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use Henri\Framework\Database\DatabaseDriver;
use Henri\Framework\Model\Entity\Helper\Query;
use Henri\Framework\Model\Entity\Helper\Translate;

abstract class Entity
{

	/**
	 * @var DatabaseDriver $database
	 */
	protected $database;

	/**
	 * @var AnnotationReader $annotationReader  Doctrine Annotation Reader
	 */
	protected $annotationReader;

	/**
	 * @var Translate $helperTranslate
	 */
	protected $helperTranslate;

	/**
	 * @var Query $helperQuery
	 */
	protected $helperQuery;

	/**
	 * @var \ReflectionClass  $reflectionClass  Reflection of this class
	 */
	protected $reflectionClass;

	/**
	 * @var string  $primaryKey the primary key in the table
	 */
	protected $primaryKey;

	/**
	 * @var array $propertyMap  map of properties and their belonging name in the table
	 */
	protected $propertyMap = array();

	/**
	 * @var array $propertyActions  actions to related to properties
	 */
	protected $propertyActions = array();

	/**
	 * @var array $propertyProps  properties and their props/settings
	 */
	protected $propertyProps = array();

	/**
	 * @var string  $tableName  table name without prefix
	 */
	protected $tableName;

	/**
	 * @var string  $tableNamePrefixed  prefixed version of the table name
	 */
	protected $tableNamePrefixed;

	/**
	 * Entity constructor.
	 *
	 * @param DatabaseDriver   $databaseDriver
	 * @param AnnotationReader $annotationReader
	 * @param Translate        $helperTranslate
	 * @param Query            $helperQuery
	 *
	 * @throws \ReflectionException
	 */
	public function __construct(
			DatabaseDriver $databaseDriver,
			AnnotationReader $annotationReader,
			Translate $helperTranslate,
			Query $helperQuery
	) {
		$this->database         = $databaseDriver;
		$this->annotationReader = $annotationReader;
		$this->helperTranslate  = $helperTranslate;
		$this->helperQuery      = $helperQuery;
		$this->reflectionClass  = new \ReflectionClass(get_class($this));

		$this->setTable();
		$this->mapProperties();
	}

	/**
	 * Method to populate object state
	 *
	 * @param array|\stdClass $state      state values to populate
	 * @param bool            $autoload   whether to try to autoload the object based on the given data
	 *
	 * @return void
	 */
	public function populateState($state, bool $autoload) : void {
		if (!is_array($state)) {
			$state = (array) $state;
		}

		foreach ($state as $propertyName => $value) {
			// Check if property is mapped for db usage
			if (!$this->hasProperty($propertyName)) {
				continue;
			}

			$this->{$propertyName} = $value;
		}

		if ($autoload) {
			$this->load();
		}
	}

	/**
	 * Method to populate state from db values
	 *
	 * @param $state
	 *
	 * @return void
	 */
	public function populateStateFromDB($state) : void {
		if (!is_array($state)) {
			$state = (array) $state;
		}

		foreach ($state as $key => $value) {
			$property = array_search($key, $this->propertyMap);
			if ($property && property_exists($this, $property)) {
				$value = $this->onAfterLoad($value, $property);
				$this->{$property} = $value;
			}
		}
	}

	/**
	 * Method to load state from db based on populated properties
	 *
	 * @param array $keys   keys to load
	 * @param array $loadBy keys to use a reference for load
	 *
	 * @return bool true on succes. false on not found
	 */
	public function load(array $keys = array(), array $loadBy = array()) : bool {
		$query = $this->database->select('[*]')->from('[' . $this->tableNamePrefixed . ']');
		$where = '';
		foreach ($this->propertyMap as $propertyName => $dbKey) {
			if (!isset($this->{$propertyName})) {
				continue;
			}

			$value = $this->onBeforeSave($this->{$propertyName}, $propertyName);

			$query->where($dbKey . ' = %s', $value);
		}

		$result = $query->fetch();

		if (is_null($result)) {
			return false;
		}

		$result = $result->toArray();

		if (empty($result)) {
			return false;
		}

		$this->populateStateFromDB($result);

		return true;
	}

	/**
	 * Method to save/update based on the current state
	 *
	 * @return bool
	 * @throws \Dibi\Exception
	 */
	public function save() : bool {
		// Check if record is new
		$isNew = isset($this->{$this->primaryKey}) && $this->{$this->primaryKey} ? false : true;

		if ($isNew) {
			// Insert
			$this->database->query('INSERT INTO ' . $this->tableNamePrefixed, $this->getValuesForDatabase());
			if ($this->database->getInsertId()) {
				$this->{$this->primaryKey} = $this->database->getInsertId();
			}
		} else {
			// Update
			$this->database->query(
				'UPDATE ' . $this->tableNamePrefixed . ' SET',
				$this->getValuesForDatabase(),
				'WHERE ' . $this->primaryKey . ' = ?', $this->{$this->primaryKey}
			);
		}

		return true;
	}

	/**
	 * Method to delete a row from the database
	 *
	 * @return bool
	 * @throws \Dibi\Exception
	 */
	public function delete() : bool {
		if (!isset($this->{$this->primaryKey}) || !$this->{$this->primaryKey}) {
			throw new Exception('No item found to remove', 500);
		}

		$this->database->query(
			'DELETE FROM ' . $this->tableNamePrefixed . ' 
					WHERE ' . $this->primaryKey . ' = ' . $this->{$this->primaryKey}
					);

		return true;
	}

	/**
	 * Method to get all properties as array
	 *
	 * @return array
	 */
	public function getValuesAsArray(bool $preparedForSave = false) : array {
		$values = array();
		foreach ($this->propertyMap as $propertyName => $dbBinding) {
			$value = $preparedForSave && $this->{$propertyName} ? $this->onBeforeSave($this->{$propertyName}, $propertyName) : $this->{$propertyName};
			$values[$propertyName] = $value;
		}

		return $values;
	}

	/**
	 * @param string $name
	 * @param array  $args
	 *
	 * @return \stdClass
	 */
	public function getValuesAsObject(bool $preparedForSave = false) : \stdClass {
		$values = new \stdClass();
		foreach ($this->propertyMap as $propertyName => $dbBinding) {
			$value = $preparedForSave && $this->{$propertyName} ? $this->onBeforeSave($this->{$propertyName}, $propertyName) : $this->{$propertyName};
			$values->{$propertyName} = $value;
		}

		return $values;
	}

	/**
	 * Method to get all properties as array with db bindings as key
	 *
	 * @return array
	 */
	protected function getValuesForDatabase() : array {
		$values = array();
		foreach ($this->propertyMap as $propertyName => $dbBinding) {
			$value = $this->{$propertyName};
			$value = $this->onBeforeSave($value, $propertyName);

			$values[$dbBinding] = $value;
		}

		return $values;
	}

	/**
	 * On before save event
	 *
	 * @param $value
	 * @param $propertyName
	 *
	 * @return mixed
	 * @throws Exception
	 */
	protected function onBeforeSave($value, $propertyName) {
		if (array_key_exists($propertyName, $this->propertyActions['translate'])) {
			foreach ($this->propertyActions['translate'][$propertyName] as $action) {
				if (!method_exists($this->helperTranslate, $action)) {
                    throw new Exception('Method ' . $action . ' not found in ' . get_class($this->helperTranslate), 500);
				}
				$value = $this->helperTranslate->{$action}($value, true);
			}
		}

		return $value;
	}

	/**
	 * On after load event
	 *
	 * @param $value
	 * @param $propertyName
	 *
	 * @return mixed
	 * @throws Exception
	 */
	protected function onAfterLoad($value, $propertyName) {
		if (strlen($value) && array_key_exists($propertyName, $this->propertyActions['translate'])) {
			foreach ($this->propertyActions['translate'][$propertyName] as $action) {
				if (!method_exists($this->helperTranslate, $action)) {
                    throw new Exception('Method ' . $action . ' not found in ' . get_class($this->helperTranslate), 500);
				}
				$value = $this->helperTranslate->{$action}($value, false);
			}
		}

		return $value;
	}

	/**
	 * Method to get property where clause
	 *
	 * @param string $propertyName  name of the property to get
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getPropertyWhereClause(string $propertyName) : array {
		if (!$this->hasProperty($propertyName)) {
			throw new Exception('Property ' . $propertyName . ' not found', 500);
		}

		return array(
				'prepare' =>  $this->propertyMap[$propertyName] . ' = %s ',
				'value'   =>  $this->{$propertyName}
		);
	}

	/**
	 * Method to validate if a property is available
	 *
	 * @param string $property
	 *
	 * @return bool
	 */
	public function hasProperty(string $property) : bool {
		return array_key_exists($property, $this->propertyMap);
	}

	/**
	 * Method to get property from object
	 *
	 * @param string $property
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function get(string $property) {
		// Only this class itself or EntityManager are allowed access
		$calling_class = debug_backtrace(1, 1)[0]['class'];
		if (!is_a($calling_class, 'Henri\Framework\Model\Entity\EntityManager', true) &&
				!is_a($calling_class, 'Henri\Framework\Model\Entity\Entity', true)) {
			throw new Exception('Access to method not allowed', 500);
		}

		if (!property_exists($this, $property)) {
			throw new Exception('Property not found', 500);
		}

		return $this->{$property};
	}

	/**
	 * Method to get property's db name
	 *
	 * @param string $property
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getPropertyDBName(string $property) : string {
		// Only this class itself or EntityManager are allowed access
		$calling_class = debug_backtrace(1, 1)[0]['class'];
		if (!is_a($calling_class, 'Henri\Framework\Model\Entity\EntityManager', true) &&
				!is_a($calling_class, 'Henri\Framework\Model\Entity\Entity', true)) {
			throw new Exception('Access to method not allowed', 500);
		}

		if (!$this->hasProperty($property)) {
			throw new Exception('Property ' . $property . ' does not exist for ' . get_class($this), 500);
		}

		return $this->propertyMap[$property];
	}

	/**
	 * Method to set table
	 *
	 * @return void
	 */
	protected function setTable() : void {
		$annotation = $this->annotationReader->getClassAnnotation($this->reflectionClass, 'Henri\Framework\Annotations\Annotation\DB');
		if ($annotation && isset($annotation->table)) {
			$this->tableName = $annotation->table;
			$this->tableNamePrefixed = $this->database->getPrefix() . $this->tableName;
		}
	}

	/**
	 * Method to get table name
	 *
	 * @return string
	 */
	public function getTableName(bool $prefixed = true) : string {
		return $prefixed ? $this->tableNamePrefixed : $this->tableName;
	}

	/**
	 * Method to map object properties to table columns
	 *
	 * @return void
	 */
	protected function mapProperties() : void {
		$this->propertyActions['translate'] = array();
		$properties = $this->reflectionClass->getProperties();
		foreach ($properties as $property) {
			$annotation = $this->annotationReader->getPropertyAnnotation($property, 'Henri\Framework\Annotations\Annotation\DBRecord');
			if (isset($property->name) && $annotation && isset($annotation->name)) {
				$this->propertyMap[$property->name] = $annotation->name;

				$propertyProps              = new \stdClass();
				$propertyProps->name        = $annotation->name;
				$propertyProps->type        = isset($annotation->type) && !empty($annotation->type) ? $annotation->type : 'text';
				$propertyProps->length      = $annotation->length;
				$propertyProps->primary     = $annotation->primary;
				$propertyProps->translate   = $annotation->translate;
				$propertyProps->empty       = $annotation->empty;
				$propertyProps->unique      = $annotation->unique ?? false;
				$this->propertyProps[$property->name] = $propertyProps;
			}

			if (isset($property->name) && $annotation && isset($annotation->translate) && !empty($annotation->translate)) {
				// Set translate actions
				$this->propertyActions['translate'][$property->name] = $annotation->translate;
			}

			// Check for primary key
			if (isset($property->name) && $annotation && isset($annotation->primary)) {
				if ($annotation->primary && isset($this->primaryKey)) {
					throw new Exception('Multiple primary keys found', 500);
				}

				if ($annotation->primary) {
					$this->primaryKey = $property->name;
				}
			}
		}
	}

	/**
	 * Method to get property name by db name
	 *
	 * @param string $dbName
	 *
	 * @return string|null
	 */
	private function getPropertyNameByDbName(string $dbName) : ?string {
		$property = array_search($dbName, $this->propertyMap);
		if ($property && property_exists($this, $property)) {
			return $property;
		}

		return null;
	}

	/**
	 * Method to reset properties
	 */
	public function reset() : void {
		foreach ($this->propertyMap as $propertyName => $dbName) {
			$this->{$propertyName} = null;
		}
	}

	/**
	 * Method to create entity table
	 *
	 * @param bool $dropTableIfExists
	 *
	 * @return bool
	 * @throws \Dibi\Exception
	 */
	public function createTable(bool $dropTableIfExists = false) : bool {
		if ($dropTableIfExists) {
			$this->database->query('DROP TABLE if EXISTS ' . $this->tableNamePrefixed);
		}

		$query = 'CREATE TABLE ' . $this->tableNamePrefixed . ' (';
		$count = 1;
		foreach ($this->propertyProps as $propertyProp) {
			$query .= $this->helperQuery->getCreateQueryForProperty($propertyProp);
			$query .= $count < count($this->propertyProps) ? ',' : '';
			$count++;
		}
		if ($this->primaryKey) {
			$query .= ',PRIMARY KEY (' . $this->propertyMap[$this->primaryKey] . ')';
		}
		$query .= ' );';

		$this->database->query($query);

		return true;
	}

	/**
	 * Method to update a table by entity properties
	 *
	 * @param bool $removeNonExistingColumns
     * @param bool $dropTableIfExists
	 *
	 * @return array    Array of non properties which only exist in the database and are not present as properties.
	 *                  If $removeNonExistingColumns = true, then will have been dropped from the table.
	 *                  Mind this is dangerous in a production environment!
	 * @throws \Dibi\Exception
	 */
	public function updateTable(bool $removeNonExistingColumns, bool $dropTableIfExists) : array {
		$currentColumns         = $this->getTableColumns();
		$nonExistingColumns     = array();

		if (is_null($currentColumns)) {
			// Table does not exist yet
			$this->createTable(false);
			throw new Exception('Table ' . $this->tableNamePrefixed . ' did not exist yet. It has been created.');
		}
		if (!is_null($currentColumns) && $dropTableIfExists) {
            // Table dropped and newly created
            $this->createTable(false);
            throw new Exception('Table ' . $this->tableNamePrefixed . ' has been dropped and recreated.');
        }

		$query = 'ALTER TABLE ' . $this->tableNamePrefixed . ' ';

		$count = 1;
		foreach ($this->propertyProps as $propertyProp) {
			if (array_key_exists($propertyProp->name, $currentColumns)) {
				// Column already present, create update query
				$query .= $this->helperQuery->getUpdateQueryForProperty($propertyProp, true);
			} else {
				// Column is new, create add query
				$query .= $this->helperQuery->getUpdateQueryForProperty($propertyProp, false);
			}
			$query .= $count < count($this->propertyProps) ? ',' : '';
			$count++;
		}

		foreach ($currentColumns as $column) {
			$propertyName   = $this->getPropertyNameByDbName($column->COLUMN_NAME);
			if (!$propertyName || !$this->hasProperty($propertyName)) {
				// Column present in database but not in entity
				array_push($nonExistingColumns, $column->COLUMN_NAME);
				if ($removeNonExistingColumns) {
					$query .= ',' . $this->helperQuery->getRemoveQueryForProperty($column->COLUMN_NAME);
				}
			}
		}

		$this->database->query($query);

		return $nonExistingColumns;
	}

	/**
	 * Method to get table columns for entity
	 *
	 * @return array|null   associative array of columns, null when table does not exist
	 * @throws \Dibi\Exception
	 */
	private function getTableColumns() : ?array {
		$query = 'SELECT column_name, data_type, column_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N\'' . $this->tableNamePrefixed . '\'';
		$columns    = $this->database->query($query);

		$columnsArr = array();
		foreach ($columns as $column) {
		    if (property_exists($column, 'COLUMN_NAME')) {
                $columnsArr[$column->COLUMN_NAME]   = $column;
            }
            if (property_exists($column, 'column_name')) {
                $column->COLUMN_NAME = $column->column_name;
                $columnsArr[$column->COLUMN_NAME]   = $column;
            }
		}

		return !empty($columnsArr) ? $columnsArr : null;
	}


}