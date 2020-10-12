<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 14-12-2019 20:02
 */

namespace Henri\Framework\Router;

class HTTPRequest {

	/**
	 * @var Request $request
	 */
	public $request;

	/**
	 * HTTPRequest constructor.
	 */
	public function __construct(
			Request $request
	) {
		$this->request      = $request;
	}


}