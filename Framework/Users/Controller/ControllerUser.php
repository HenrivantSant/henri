<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 30-11-2019 12:27
 */

namespace Henri\Framework\Users\Controller;

use Henri\Framework\Controller\Controller;
use Henri\Framework\Http\Response\JSONResponse;
use Henri\Framework\Router\HTTPRequest;
use Henri\Framework\Users\Model\User as ModelUser;
use Henri\Framework\Authentication\Auth;
use Henri\Framework\Users\Helper\User as HelperUser;

use Henri\Framework\Annotations\Annotation\Route;
use Symfony\Component\Console\Helper\Helper;

class ControllerUser extends Controller
{
	/**
	 * @var ModelUser $modelUser
	 */
	private $modelUser;

	/**
	 * @var Auth $auth
	 */
	private $auth;

	/**
	 * @var HelperUser $helperUser
	 */
	private $helperUser;

	/**
	 * ControllerUser constructor.
	 *
	 * @param HTTPRequest $HTTPRequest
	 * @param ModelUser   $modelUser
	 * @param Auth        $auth
	 * @param HelperUser  $helperUser
	 *
	 * @Route(type="GET|POST|PUT", route="/users/user/", authRequired=true)
	 */
	public function __construct(
			HTTPRequest $HTTPRequest,
			ModelUser $modelUser,
			Auth $auth,
			HelperUser $helperUser
	) {
		$this->modelUser    = $modelUser;
		$this->auth         = $auth;
		$this->helperUser   = $helperUser;
		parent::__construct($HTTPRequest);
	}

	/**
	 * Method to verify user login
	 *
	 * @Route(type="POST", route="/login/")
	 *
	 * @return JSONResponse
	 * @throws \Exception
	 */
	public function login() : JSONResponse {
		$data       = $this->auth->decode('secret', $this->HTTPRequest->request->input->get('payload'));

		$username   = !empty($data['username']) ? $data['username'] : false;
		$password   = !empty($data['password']) ? $data['password'] : false;

		if (!$username || !$password || !$this->modelUser->userMayLogin($username, $password)) {
			// User not allowed to log in
			$response   = new JSONResponse();
			$response::notAuthorized();
			return $response;
		}

		$user = $this->modelUser->getPopulatedUser();

		// Update token
		$token  = $this->auth->getToken();
		$token->userID  = $user->id;
		$token->level   = 'login';
		$this->auth->updateToken($token);

		$response   = new JSONResponse(array(
			'state' => 'logged in',
		));

		return $response;
	}

	/**
	 * @Route(type="POST", route="/create/")
	 *
	 * @return JSONResponse
	 */
	public function create() : JSONResponse {
		$username   = $this->HTTPRequest->request->input->get('username');
		$password   = $this->HTTPRequest->request->input->get('password');
		$email      = $this->HTTPRequest->request->input->get('email') ? $this->HTTPRequest->request->input->get('email') : '';

		$this->modelUser->createUser($username, $password, $email);

		$response   = new JSONResponse(array('state' => 'created'));

		return $response;
	}

	/**
	 * Method to get the current logged in user
	 *
	 * @Route(type="GET", route="/me/")
	 *
	 * @return JSONResponse
	 */
	public function getUserData() : JSONResponse {
		$userID = $this->helperUser->getCurrentUserID();

		if (is_null($userID)) {
			$response   = new JSONResponse();
			$response::notFound();
			return $response;
		}
		$user   = $this->modelUser->getUserByID($userID);

		if (is_null($user)) {
			$response   = new JSONResponse();
			$response::notFound();
			return $response;
		}

		return new JSONResponse($user);
	}


}