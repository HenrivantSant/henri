<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 14-12-2019 17:33
 */

namespace Henri\Framework\Annotations\Annotation;

/**
 * Class Route
 * @package Henri\Framework\Annotations\Annotation
 *
 * @Annotation
 * @Target("METHOD")
 */
class Route {

	/**
	 * @var string  $type
	 */
	public $type;

	/**
	 * @var string  $route
	 */
	public $route;

    /**
     * @var string $name
     */
	public $name;

	/**
	 * @var bool    $authRequired
	 */
	public $authRequired;

	/**
	 * @var string  $authLevel
	 */
	public $authLevel;

	public function __construct(array $values = array()) {
		$this->type         = !empty($values['type']) ? $values['type'] : '';
		$this->route        = !empty($values['route']) ? $values['route'] : '';
		$this->name         = !empty($values['name']) ? $values['name'] : '';
		$this->authRequired = !(empty($values['authRequired'])) ? $values['authRequired'] : false;

		$possibleAuthlevels = array('token', 'login');
		$this->authLevel    = !empty($values['authLevel']) && in_array($values['authLevel'], $possibleAuthlevels) ? $values['authLevel'] : 'token';

	}
}