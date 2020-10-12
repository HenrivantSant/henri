<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 20-12-2019 21:03
 */

namespace Henri\Framework\Http\Response;


abstract class Response
{

	/**
	 * @var array|\stdClass
	 */
	protected $response;

	/**
	 * @var string  $defaultResponse
	 */
	private static $defaultResponse;

	/**
	 * Response constructor.
	 */
	public function __construct() {

	}

	/**
	 * @param array|\stdClass $response
	 */
	public function setResponse($response): void {
		$this->response = $response;
	}

	/**
	 * Method to get response
	 *
	 * @return array|\stdClass
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * Wrapper method for doOutput()
	 */
	public function sendOutput() : void {
		//header("Access-Control-Allow-Origin: app.henrivantsant.com");
		header("Access-Control-Allow-Credentials: true");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		header("Vary: Accept-Encoding, Origin");
		header("Keep-Alive: timeout=2, max=100");
		header("Connection: Keep-Alive");
		header("Content-Type: text/plain");

		try {
			if (!isset($this->response) && isset(self::$defaultResponse)) {
				$methodName = 'response' . ucfirst(self::$defaultResponse);
				if (method_exists($this, $methodName)) {
					$this->{$methodName}();
				} else {
					throw new \Exception('Default response does not exist', 500);
				}
			} else {
				if (!isset($this->response)) {
					throw new \Exception('No output', 500);
				}

				$this->doOutput();
			}
		} catch (\Exception $exception) {
			self::internalError();
			$this->{self::$defaultResponse}();
			exit();
		}

		exit();
	}

	abstract protected function doOutput() : void;

	public static function notAuthorized() : void {
		self::$defaultResponse  = 'notAuthorized';
	}

	protected function responseNotAuthorized() : void {
		header("HTTP/1.1 401 Unauthorized");
		header('Status:' . 401);
		header('Message: Not authorized');
	}

	public static function notFound() : void {
		self::$defaultResponse  = 'notFound';
	}

	protected function responseNotFound() : void {
		header("HTTP/1.1 404 Not found");
		header('Status:' . 404);
		header('Message: Not found');
	}

	public static function internalError() : void {
		self::$defaultResponse  = 'internalError';
	}

	protected function responseInternalError() : void {
		header("HTTP/1.1 500 Internal error");
		header('Status:' . 500);
		header('Message: Internal error');
	}


}