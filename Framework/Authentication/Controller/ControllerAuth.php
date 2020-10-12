<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 30-11-2019 12:27
 */

namespace Henri\Framework\Authentication\Controller;

use Henri\Framework\Controller\Controller;
use Henri\Framework\Authentication\Auth;
use Henri\Framework\Authentication\Model\Client;
use Henri\Framework\Http\Response\JSONResponse;
use Henri\Framework\Router\HTTPRequest;

use Henri\Framework\Annotations\Annotation\Route;

class ControllerAuth extends Controller
{

	/**
	 * @var Client $modelClient
	 */
	private $modelClient;

	/**
	 * @var Auth $auth
	 */
	private $auth;

	/**
	 * ControllerAuth constructor.
	 *
	 * @param HTTPRequest $HTTPRequest
	 *
	 * @Route(type="GET", route="/auth/", authRequired=false)
	 */
	public function __construct(
			HTTPRequest $HTTPRequest,
			Client $modelClient,
			Auth $auth
	) {
		$this->modelClient  = $modelClient;
		$this->auth         = $auth;
		parent::__construct($HTTPRequest);
	}

	/**
	 * Method to retieve a token
	 *
	 * @param array $params
	 *
	 * @Route(type="GET|POST", route="/get/token/[a:apikey]")
	 *
	 * @return JSONResponse
	 */
	public function getToken(array $params = array()) : JSONResponse {
		// TODO: Verify token
		// Token = apikey, registered for a given domain and has is base64 encoded version of secret key + apikey

		$domain = $this->HTTPRequest->request->getRequest()['HTTP_HOST'] === 'api.henrivantsant.com' ? 'self' : $this->HTTPRequest->request->getRequest()['HTTP_HOST'];
		$client = $this->modelClient->getClientByApiKeyAndDomain($params['apikey'], $domain);
		if (is_null($client)) {
			$response = new JSONResponse();
			$response::notAuthorized();
			return $response;
		}

		// TODO: Check if the base64 encoded version of the secret key + apikey === the header
		// Make the Auth class do this

		// TODO: If this is correct generate a token and submit it back to the response
		// Make the Auth class do this
		$responseToken  = $this->auth->generateToken($client->secret, $client);

		$this->modelClient->saveToken($client->id, $responseToken, 'token');

		$response = new JSONResponse(array(
			'token' => $responseToken,
		));

		return $response;
	}


}