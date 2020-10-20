<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 15-12-2019 16:55
 */

namespace Henri\Framework\Logging\Entity;

use Doctrine\Common\Annotations\AnnotationReader;
use Henri\Framework\Annotations\Annotation\DB;
use Henri\Framework\Annotations\Annotation\DBRecord;
use Henri\Framework\Database\DatabaseDriver;
use Henri\Framework\Model\Entity\Entity;
use Henri\Framework\Model\Entity\Helper\Query;
use Henri\Framework\Model\Entity\Helper\Translate;

/**
 * Class Log
 * @package Henri\Framework\Logging\Entity\Log
 *
 * @DB(table="log")
 */
class LogEntity extends Entity {

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
	 * @var string  $channel
	 * @DBRecord(
	 *     name="channel",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $channel;

	/**
	 * @var string  $message
	 * @DBRecord(
	 *     name="message",
	 *     type="text"
	 * )
	 */
	protected $message;

    /**
     * @var int $level
     * @DBRecord (
     *     name="level",
     *     type="int",
     *     length=11
     * )
     */
	protected $level;

    /**
     * @var string  $levelName
     * @DBRecord(
     *     name="level_name",
     *     type="text",
     *     length=255
     * )
     */
    protected $levelName;

    /**
     * @var stdClass   $context
     * @DBRecord(
     *     name="context",
     *     type="text",
     *     translate="json"
     * )
     */
    protected $context;

    /**
     * @var string  $datetime
     * @DBRecord(
     *     name="datetime",
     *     type="datetime",
     *     translate="datetime"
     * )
     */
    protected $datetime;

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