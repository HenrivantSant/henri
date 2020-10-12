<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 6-5-2020 21:25
 */

namespace Henri\Framework\Router\Model\Entity;

use Doctrine\Common\Annotations\AnnotationReader;
use Henri\Framework\Database\DatabaseDriver;
use Henri\Framework\Model\Entity\Entity;
use Henri\Framework\Model\Entity\Helper\Query;
use Henri\Framework\Model\Entity\Helper\Translate;
use Henri\Framework\Annotations\Annotation\DB;
use Henri\Framework\Annotations\Annotation\DBRecord;

/**
 * Class LogRequest
 * @package Henri\Framework\Router\Model\Entity
 *
 * @DB(table="log_request")
 */
class LogRequest extends Entity {

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
	 * @var string  $ip
	 * @DBRecord(
	 *     name="ip",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $ip;

	/**
	 * @var string  $origin
	 * @DBRecord(
	 *     name="origin",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $origin;

	/**
	 * @var string  $time
	 * @DBRecord(
	 *     name="time",
	 *     type="datetime",
	 *     translate="datetime"
	 * )
	 */
	protected $time;

	/**
	 * @var string  $method
	 * @DBRecord(
	 *     name="method",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $method;

	/**
	 * @var \stdClass   $headers
	 * @DBRecord(
	 *     name="headers",
	 *     type="text",
	 *     translate="json"
	 * )
	 */
	protected $headers;

	/**
	 * @var \stdClass   $body
	 * @DBRecord(
	 *      name="body",
	 *      type="text",
	 *      translate="json"
	 * )
	 */
	protected $body;

	/**
	 * @var int $code
	 * @DBRecord(
	 *     name="code",
	 *     type="int",
	 *     length=11
	 * )
	 */
	protected $code;

	/**
	 * LogRequest constructor.
	 *
	 * @param DatabaseDriver   $databaseDriver
	 * @param AnnotationReader $annotationReader
	 * @param Translate        $helperTranslate
	 * @param Query            $helperQuery
	 *
	 * @throws \ReflectionException
	 */
	public function __construct(
		DatabaseDriver      $databaseDriver,
		AnnotationReader    $annotationReader,
		Translate           $helperTranslate,
		Query               $helperQuery
	) {
		parent::__construct($databaseDriver, $annotationReader, $helperTranslate, $helperQuery);
	}
}