<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 13-12-2019 20:43
 */

namespace Henri\Framework\Annotations\Annotation;

/**
 * Class DBRecord
 * @package Henri\Framework\Annotations\Annotation
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class DBRecord {

	/**
	 * @var string  $name
	 * @Required
	 */
	public $name;

	/**
	 * @var bool  $primary  whether this is the primary key
	 */
	public $primary;

	/**
	 * @var string $type
	 */
	public $type = 'text';

	/**
	 * @var array $translate  translate actions to perform (eg. json)
	 */
	public $translate;

	/**
	 * @var int $length
	 */
	public $length;

	/**
	 * @var bool $empty
	 */
	public $empty;


	/**
	 * DBRecord constructor.
	 *
	 * @param array $values
	 */
	public function __construct(array $values = array()) {
		$this->name       = $values['name'];
		$this->primary    = isset($values['primary']) ? $values['primary'] : false;
		$this->type       = !empty($values['type']) ? $values['type'] : 'text';
		$this->translate  = !empty($values['translate']) ? explode(' ', $values['translate']) : array();
		$this->length     = !empty($values['length']) ? intval($values['length']) : 0;
		$this->empty      = !empty($values['empty']) ? boolval($values['empty']) : false;
	}


}