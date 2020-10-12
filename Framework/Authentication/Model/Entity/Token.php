<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 6-5-2020 21:25
 */

namespace Henri\Framework\Authentication\Model\Entity;

use Doctrine\Common\Annotations\AnnotationReader;
use Henri\Framework\Database\DatabaseDriver;
use Henri\Framework\Model\Entity\Entity;
use Henri\Framework\Model\Entity\Helper\Query;
use Henri\Framework\Model\Entity\Helper\Translate;
use Henri\Framework\Annotations\Annotation\DB;
use Henri\Framework\Annotations\Annotation\DBRecord;

/**
 * Class Token
 * @package Henri\Framework\Users\Model\Entity
 *
 * @DB(table="token")
 */
class Token extends Entity {

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
	 * @var string  $value
	 * @DBRecord(
	 *     name="value",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $value;

	/**
	 * @var string  $expirationDate
	 * @DBRecord(
	 *     name="expiration",
	 *     type="datetime",
	 *     translate="datetime"
	 * )
	 */
	protected $expirationDate;

	/**
	 * @var int  $clientID
	 * @DBRecord(
	 *     name="client_id",
	 *     type="int",
	 *     length=11
	 * )
	 */
	protected $clientID;

	/**
	 * @var string  $userID
	 * @DBRecord(
	 *     name="user_id",
	 *     type="int",
	 *     length=11
	 * )
	 */
	protected $userID;

	/**
	 * @var string  $level
	 * @DBRecord(
	 *     name="level",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $level;

	/**
	 * Token constructor.
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