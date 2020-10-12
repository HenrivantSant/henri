<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 6-5-2020 21:25
 */

namespace Henri\Framework\Users\Model\Entity;

use Doctrine\Common\Annotations\AnnotationReader;
use Henri\Framework\Database\DatabaseDriver;
use Henri\Framework\Model\Entity\Entity;
use Henri\Framework\Model\Entity\Helper\Query;
use Henri\Framework\Model\Entity\Helper\Translate;
use Henri\Framework\Annotations\Annotation\DB;
use Henri\Framework\Annotations\Annotation\DBRecord;
use ReflectionException;

/**
 * Class User
 * @package Henri\Framework\Users\Model\Entity
 *
 * @DB(table="users")
 */
class User extends Entity {

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
	 * @var string  $username
	 * @DBRecord(
	 *     name="username",
	 *     type="text",
	 *     length=128
	 * )
	 */
	protected $username;

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
	 * @var string  $email
	 * @DBRecord(
	 *     name="email",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $email;

	/**
	 * @var string  $password
	 * @DBRecord(
	 *     name="password",
	 *     type="text",
	 *     length=255
	 * )
	 */
	protected $password;

	/**
	 * User constructor.
	 *
	 * @param DatabaseDriver   $databaseDriver
	 * @param AnnotationReader $annotationReader
	 * @param Translate        $helperTranslate
	 * @param Query            $helperQuery
	 *
	 * @throws ReflectionException
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