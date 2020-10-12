<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 15-5-2020 22:54
 */

namespace Henri\Framework\Model\Entity\Helper;


class Property {

	/**
	 * Method to get property query
	 *
	 * @param $property
	 *
	 * @return string
	 */
	public function getPropertyQuery($property) : string {
		$propertyString = $property->name;

		$propertyString = $this->appendTypeAndLenght($property, $propertyString);
		$propertyString = $this->appendOther($property, $propertyString);

		return $propertyString;
	}

	/**
	 * Method to generate type and (optional) length property
	 *
	 * @param \stdClass $property
	 * @param string    $propertyString
	 *
	 * @return string
	 */
	private function appendTypeAndLenght(\stdClass $property, string $propertyString) : string {
		if ($property->type === 'text') {
			if ($property->length === 0) {
				$propertyString .= ' longtext';
			} else {
				$propertyString .= ' varchar';
			}
		} else {
			$propertyString .= ' ' . $property->type;
		}

		if ($property->length > 0) {
			$propertyString .= '(' . $property->length . ')';
		}

		return $propertyString;
	}

	/**
	 * Method to append several SQL options
	 *
	 * @param \stdClass $property
	 * @param string    $propertyString
	 *
	 * @return string
	 */
	private function appendOther(\stdClass $property, string $propertyString) : string {
		if (!$property->empty) {
			$propertyString .= ' NOT NULL';
		}

		if ($property->primary) {
			$propertyString .= ' AUTO_INCREMENT';
		}

		return $propertyString;
	}
}