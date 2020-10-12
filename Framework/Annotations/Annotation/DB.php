<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 13-12-2019 18:13
 */

namespace Henri\Framework\Annotations\Annotation;

/**
 * Class DB
 * @package Henri\Framework\Annotations\Annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
class DB {

	/**
	 * @var string  $table
	 */
	public $table;

	/**
	 * DB constructor.
	 *
	 * @param array $values
	 */
	public function __construct(array $values = array())
	{
		$this->table = $values['table'];
	}
}

