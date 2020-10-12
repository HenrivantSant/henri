<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 15-5-2020 20:16
 */

namespace Henri\Framework\Authentication\Helper;


class Token {

	/**
	 * Method to get unique token
	 *
	 * @param string $base
	 * @param int    $maxLenght
	 *
	 * @return string
	 */
	public function generateUniqueToken(string $base = null, int $maxLenght = 0) : string {
		$base   = $base ? $base : strtotime('now');
		$token  = md5(base64_encode($base . uniqid() . strtotime('now')));

		return $maxLenght ? substr($token, 0, $maxLenght) : $token;
	}

}