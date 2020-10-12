<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 20-12-2019 21:07
 */

namespace Henri\Framework\Http\Response;


class JSONResponse extends Response {


	/**
	 * JSONResponse constructor.
	 */
	public function __construct($response = array()) {
		if (( is_array($response) && !empty($response) )|| ( is_object($response) && count(get_object_vars($response)) )) {
			$this->response = $response;
		}
	}

	public function doOutput(): void {
		http_response_code(200);

		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: *");
		header('Content-Type: application/json');
		header('Status:' . 200);

		$encode = json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		echo $encode;
	}
}