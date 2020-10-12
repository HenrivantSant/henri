<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 15-12-2019 16:55
 */

namespace Henri\Framework\Model\Entity;

use Doctrine\Common\Annotations\AnnotationReader;
use Henri\Framework\Annotations\Annotation\DB;
use Henri\Framework\Annotations\Annotation\DBRecord;
use Henri\Framework\Database\DatabaseDriver;
use Henri\Framework\Model\Entity\Helper\Query;
use Henri\Framework\Model\Entity\Helper\Translate;

/**
 * Class Setting
 * @package Henri\Framework\Model\Entity
 *
 * @DB(table="settings")
 */
class Setting extends Entity {

	/**
	 * @var int $id
	 * @DBRecord(
	 *     name="id",
	 *     type="int",
	 *     primary=true,
	 *     length=11
	 * )
	 */
	protected $id;

	/**
	 * @var string  $name
	 * @DBRecord(
	 *     name="name",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $name;

	/**
	 * @var string  $value
	 * @DBRecord(
	 *     name="value",
	 *     type="text"
	 * )
	 */
	protected $value;

	/**
	 * Setting constructor.
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
		parent::__construct($databaseDriver, $annotationReader, $helperTranslate, $helperQuery);
	}
}