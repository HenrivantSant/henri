<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 10-5-2020 21:55
 */

namespace Henri\Framework\Router\Model;

use Henri\Framework\Model\Entity\EntityManagerList;
use Henri\Framework\Model\Entity\EntityManagerSingle;
use Henri\Framework\Model\HenriModelBase;
use Henri\Framework\Router\Model\Entity\LogRequest;

class Request extends HenriModelBase {

	/**
	 * @var LogRequest $entityLogRequest
	 */
	private $entityLogRequest;

	/**
	 * Request constructor.
	 *
	 * @param EntityManagerSingle $entityManagerSingle
	 * @param EntityManagerList   $entityManagerList
	 * @param LogRequest          $entityLogRequest
	 */
	public function __construct(
		EntityManagerSingle $entityManagerSingle,
		EntityManagerList $entityManagerList,
		LogRequest $entityLogRequest
	) {
		$this->entityLogRequest = $entityLogRequest;
		parent::__construct($entityManagerSingle, $entityManagerList);
	}

	/**
	 * Method to log a request
	 *
	 * @param string $ip
	 * @param string $origin
	 * @param string $time
	 * @param string $method
	 * @param array  $headers
	 * @param null   $body
	 *
	 * @throws \Dibi\Exception
	 */
	public function logRequest(string $ip, string $origin, string $time, string $method, array $headers, $body = null, int $code) : void {
		$request    = new \stdClass();
		$request->ip        = $ip;
		$request->origin    = $origin;
		$request->time      = $time;
		$request->method    = $method;
		$request->headers   = (object) $headers;
		$request->body      = $body;
		$request->code      = $code;

		$this->entityLogRequest->populateState($request, false);
		$this->entityLogRequest->save();

		if (!$this->entityLogRequest->get($this->entityLogRequest->get('primaryKey'))) {
			throw new \Exception('Error on logging request', 500);
		}
	}


}