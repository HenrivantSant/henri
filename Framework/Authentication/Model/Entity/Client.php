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
 * Class Client
 * @package Henri\Framework\Users\Model\Entity
 *
 * @DB(table="clients")
 */
class Client extends Entity {

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
	 * @var string  $apikey
	 * @DBRecord(
	 *     name="apikey",
	 *     type="text",
	 *     length=128
	 * )
	 */
	protected $apikey;

	/**
	 * @var string  $domain
	 * @DBRecord(
	 *     name="domain",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $domain;

	/**
	 * @var string  $secret
	 * @DBRecord(
	 *     name="secret",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $secret;

	/**
	 * Client constructor.
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