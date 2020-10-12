<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 30-11-2019 15:25
 */

namespace Henri\Framework\Controller;

use Henri\Framework\Router\HTTPRequest;

abstract class Controller extends AbstractController {

	/**
	 * Controller constructor.
	 *
	 * @param HTTPRequest $HTTPRequest
	 */
	public function __construct(
			HTTPRequest $HTTPRequest
	) {
		parent::__construct($HTTPRequest);
	}


}