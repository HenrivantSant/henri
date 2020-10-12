<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 6-5-2020 21:30
 */

namespace Henri\Framework\Users\Model;


use Dibi\Exception;
use Henri\Framework\Model\Entity\EntityManagerList;
use Henri\Framework\Model\Entity\EntityManagerSingle;
use Henri\Framework\Model\HenriModelBase;
use Henri\Framework\Users\Model\Entity\User as EntityUser;
use Henri\Framework\Users\Helper\User as HelperUser;

class User extends HenriModelBase {

	/**
	 * @var EntityUser  $entityUser
	 */
	private $entityUser;

	/**
	 * @var HelperUser $helperUser
	 */
	private $helperUser;

	/**
	 * User constructor.
	 *
	 * @param EntityManagerSingle $entityManagerSingle
	 * @param EntityManagerList   $entityManagerList
	 * @param EntityUser          $entityUser
	 */
	public function __construct(
		EntityManagerSingle $entityManagerSingle,
		EntityManagerList $entityManagerList,
		EntityUser $entityUser,
		HelperUser $helperUser
	) {
		$this->entityUser   = $entityUser;
		$this->helperUser   = $helperUser;
		parent::__construct($entityManagerSingle, $entityManagerList);
	}

	/**
	 * Method to validate whether is user is allowed to log in
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function userMayLogin(string $username, string $password) : bool {
		$user   = $this->entityUser->getValuesAsObject();
		$user->username = $username;
		$this->entityUser->populateState($user, true);
		if (is_null($this->entityUser->get($this->entityUser->get('primaryKey')))) {
			// User not found
			throw new \Exception('User not found', 500);
		}

		$user   = $this->entityUser->getValuesAsObject();

		if (!$this->helperUser->passwordCorrect($password, $user->password)) {
			// Password incorrect
			throw new \Exception('Password incorrect', 5000);
		}

		return true;
	}

	/**
	 * Method to create new user
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 *
	 * @throws \Dibi\Exception
	 */
	public function createUser(string $username, string $password, string $email = '') : void {
		// Check if user does not exist already
		$user   = $this->entityUser->getValuesAsObject();
		$user->username = $username;
		$this->entityUser->populateState($user, true);
		if (!is_null($this->entityUser->get($this->entityUser->get('primaryKey')))) {
			// User already exists
			throw new \Exception('Username already taken', 500);
		}

		// TODO: Create user
		$user->password = $this->helperUser->encryptPassword($password);
		$this->entityUser->populateState($user, false);
		$this->entityUser->save();
	}

	/**
	 * Method to get user by id
	 *
	 * @param int $userID
	 *
	 * @return \stdClass|null
	 */
	public function getUserByID(int $userID) : ?\stdClass {
		$user       = $this->entityUser->getValuesAsObject();
		$user->id   = $userID;

		$this->entityUser->populateState($user, true);
		$user       = $this->entityUser->getValuesAsObject();

		unset($user->password);

		return is_null($user->username) ? null : $user;
	}

	/**
	 * Method to get populated user from user entity
	 *
	 * @return \stdClass
	 */
	public function getPopulatedUser() : \stdClass {
		return $this->entityUser->getValuesAsObject();
	}
}