<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 10-5-2020 21:41
 */

namespace Henri\Framework\Router\Helper;

use Henri\Framework\Router\HTTPRequest;

class Validator {

	/**
	 * @var HTTPRequest $HTTPRequest
	 */
	private $HTTPRequest;

	/**
	 * Validator constructor.
	 */
	public function __construct(
		HTTPRequest $HTTPRequest
	) {
		$this->HTTPRequest  = $HTTPRequest;
	}

	/**
	 * Method to validate whether the request being made is valid
	 *
	 * @return bool
	 */
	public function requestIsValid() : bool {
		// Validate whether request is valid
		// TODO: Validation

		return true;
	}


}