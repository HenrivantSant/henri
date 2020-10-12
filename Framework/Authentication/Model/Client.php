<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 6-5-2020 22:09
 */

namespace Henri\Framework\Authentication\Model;

use Henri\Framework\Authentication\Helper\Token as HelperToken;
use Henri\Framework\Authentication\Model\Entity\Token;
use Henri\Framework\Model\Entity\EntityManagerList;
use Henri\Framework\Model\Entity\EntityManagerSingle;
use \Henri\Framework\Authentication\Model\Entity\Client as entityClient;
use Henri\Framework\Model\HenriModelBase;

class Client extends HenriModelBase {

	/**
	 * @var HelperToken $helperToken
	 */
	private $helperToken;

	/**
	 * @var entityClient $entityClient
	 */
	private $entityClient;

	/**
	 * @var Token $entityToken
	 */
	private $entityToken;

	/**
	 * Client constructor.
	 *
	 * @param EntityManagerSingle $entityManagerSingle
	 * @param EntityManagerList   $entityManagerList
	 * @param HelperToken         $helperToken
	 * @param entityClient        $entityClient
	 * @param Token               $entityToken
	 */
	public function __construct(
		EntityManagerSingle $entityManagerSingle,
		EntityManagerList $entityManagerList,
		HelperToken $helperToken,
		EntityClient $entityClient,
		Token $entityToken
	) {
		$this->helperToken      = $helperToken;
		$this->entityClient     = $entityClient;
		$this->entityToken      = $entityToken;
		parent::__construct($entityManagerSingle, $entityManagerList);
	}

	/**
	 * Method to retrieve client from database by apikey
	 *
	 * @param string $apikey
	 *
	 * @return \stdClass|null
	 * @throws \Exception
	 */
	public function getClientByApiKeyAndDomain(string $apikey, string $domain) : ?\stdClass {
		$dataOjb            = $this->entityClient->getValuesAsObject();
		$dataOjb->apikey    = $apikey;
		if ($domain !== 'self') {
			$dataOjb->domain    = $domain;
		}
		$this->entityClient->populateState($dataOjb, true);

		return $this->entityClient->get($this->entityClient->get('primaryKey')) ? $this->entityClient->getValuesAsObject() : null;
	}

	/**
	 * Method to get client by a given key
	 *
	 * @param string $searchBy
	 * @param        $value
	 *
	 * @return \stdClass|null
	 * @throws \Exception
	 */
	public function getClient(string $searchBy, $value) : ?\stdClass {
		if (!$this->entityClient->hasProperty($searchBy)) {
			throw new \Exception('Property ' . $searchBy . ' does not exist');
		}

		$dataOjb    = $this->entityClient->getValuesAsObject();
		$dataOjb->{$searchBy}   = $value;
		$this->entityClient->populateState($dataOjb, true);
		$client     = $this->entityClient->getValuesAsObject();
		foreach ($client as $key => $value) {
			if (is_null($client->{$key})) {
				return null;
			}
		}

		return $this->entityClient->getValuesAsObject();
	}

	/**
	 * Method to get token by value
	 *
	 * @param string $tokenValue
	 *
	 * @return \stdClass|null
	 */
	public function getTokenByValue(string $tokenValue) : ?\stdClass {
		$token  = $this->entityToken->getValuesAsObject();
		$token->value   = $tokenValue;
		$this->entityToken->populateState($token, true);
		$token  = $this->entityToken->getValuesAsObject();

		return is_null($token->id) ? null : $token;
	}

	/**
	 * Method to create a new client
	 *
	 * @param string      $domain
	 * @param string|null $apikey
	 * @param string|null $secret
	 *
	 * @return \stdClass
	 * @throws \Dibi\Exception
	 */
	public function createClient(string $domain, string $apikey = null, string $secret = null) : \stdClass {
		if (!$domain) {
			throw new \Exception('No input given', 500);
		}

		if (!is_null($client = $this->getClient('domain', $domain))) {
			throw new \Exception('Client ' . $domain . ' already exists with id ' . $client->id);
		}
		$this->entityClient->reset();

		$dataObj    = $this->entityClient->getValuesAsObject();
		$dataObj->domain    = $domain;
		$dataObj->apikey    = $apikey ? $apikey : $this->helperToken->generateUniqueToken($domain);
		$dataObj->secret    = $secret ? $secret : $this->helperToken->generateUniqueToken(strtotime('now'), 15);
		$this->entityClient->populateState($dataObj, false);
		$this->entityClient->save();

		return $this->entityClient->getValuesAsObject();
	}

	/**
	 * Method to save/update token
	 *
	 * @param int    $clientID
	 * @param string $tokenValue
	 * @param string $level
	 * @param int    $userID
	 * @param int    $tokenID
	 *
	 * @throws \Dibi\Exception
	 */
	public function saveToken(int $clientID, string $tokenValue, string $level = 'token', int $userID = 0, int $tokenID = 0) : \stdClass {
		$token  = $this->entityToken->getValuesAsObject();

		if ($tokenID !== 0) {
			$token->id  = $tokenID;
		}
		$token->value   = $tokenValue;

		$this->entityToken->populateState($token, true);
		$token  = $this->entityToken->getValuesAsObject();

		$token->clientID        = $clientID;
		$token->expirationDate  = date('Y-m-d H:i:s', strtotime('+ 1 day'));
		$token->userID          = $userID;
		$token->level           = $level;

		$this->entityToken->populateState($token, false);
		$this->entityToken->save();

		return $this->entityToken->getValuesAsObject();
	}
}