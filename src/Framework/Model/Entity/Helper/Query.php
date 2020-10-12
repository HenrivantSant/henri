<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 15-5-2020 22:37
 */

namespace Henri\Framework\Model\Entity\Helper;


use stdClass;

class Query {

	/**
	 * @var Property    $propertyHelper
	 */
	private $propertyHelper;

	/**
	 * Query constructor.
	 *
	 * @param Property $propertyHelper
	 */
	public function __construct(
		Property    $propertyHelper
	) {
		$this->propertyHelper   = $propertyHelper;
	}

	/**
	 * Method to create SQL string to be use to create a table row
	 *
	 * @param stdClass $property
	 *
	 * @return string
	 */
	public function getCreateQueryForProperty( stdClass $property) : string {
		return $this->propertyHelper->getPropertyQuery($property);
	}

	/**
	 * Method to get update query for property
	 *
	 * @param stdClass $property
	 * @param bool      $isUpdate
	 *
	 * @return string
	 */
	public function getUpdateQueryForProperty( stdClass $property, bool $isUpdate) : string {
		return $isUpdate ?
            'MODIFY COLUMN ' . $this->propertyHelper->getPropertyQuery($property) :
            'ADD ' . $this->propertyHelper->getPropertyQuery($property);
    }

	/**
	 * Method to get remove query
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function getRemoveQueryForProperty(string $name) : string {
		return 'DROP COLUMN ' . $name;
	}
}