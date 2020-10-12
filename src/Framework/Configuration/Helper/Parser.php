<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 10-5-2020 19:34
 */

namespace Henri\Framework\Configuration\Helper;


class Parser {

	/**
	 * Method to parse array of config files to object
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	public function parseConfig(array $config): array {
		$configuration  = new \stdClass();
		$configuration->general = new \stdClass();
		$configuration->scoped  = new \stdClass();
		$map = new \stdClass();
		$map->general = array();
		$map->scoped = array();

		foreach ($config as $scopePath => $items) {
			$scope  = $this->parseScope($scopePath);

			foreach ($items as $category => $item) {
				if ($category === 'imports') {
					continue;
				}
				if ($scope->area !== 'app') {
					$configuration->general = $this->appendSettings($configuration->general, $category, $item);
					$map->general = $this->appendMapping($map->general, $category, $item, $scopePath);
				}

				if (!property_exists($configuration->scoped, $scope->scope)) {
					$configuration->scoped->{$scope->scope} = new \stdClass();
				}
				if (!array_key_exists($scope->scope, $map->scoped)) {
				    $map->scoped[$scope->scope] = array();
                }
				$configuration->scoped->{$scope->scope} = $this->appendSettings($configuration->scoped->{$scope->scope}, $category, $item);
				$map->scoped[$scope->scope] = $this->appendMapping($map->scoped[$scope->scope], $category, $item, $scopePath);
			}
		}

		return array(
		    'config'    => $configuration,
            'map'       => $map,
        );
	}

	/**
	 * Method to parse scope data
	 *
	 * @param string $scopePath
	 *
	 * @return \stdClass
	 */
	public function parseScope(string $scopePath) : \stdClass {
		$scopeArea = str_replace(INCLUDE_DIR, '', $scopePath);
		$scopeArea = str_replace('config.yaml', '', $scopeArea);

		$scope  = new \stdClass();
		$scope->path    = $scopePath;
		$scope->name    = $scopeArea === '/' ? 'root' : trim($scopeArea, '/');
		$scope->name    = substr($scope->name, 0, 12) === 'vendor/henri' ? 'framework' : $scope->name;
		$scope->scope   = $scope->name;

		$explode = explode('/', $scope->scope);
		$scope->area    = is_array($explode) && count($explode) ? $explode[0] : 'root';

		return $scope;
	}

	/**
	 * Method to parse settings mapping
	 *
	 * @param \stdClass $baseClass
	 * @param string    $settingName
	 * @param \stdClass $settings
	 *
	 * @return \stdClass
	 */
	private function appendSettings(\stdClass $baseClass, string $settingName, $settings) : \stdClass {
        if (!property_exists($baseClass, $settingName)) {
            $baseClass->{$settingName}  = new \stdClass();
        }

        foreach ($settings as $name => $value) {
            $baseClass->{$settingName}->{$name} = $value;
        }

        return $baseClass;
	}

    /**
     * Method to append settings
     *
     * @param \stdClass $baseClass
     * @param string    $settingName
     * @param \stdClass $settings
     *
     * @return \stdClass
     */
    private function appendMapping(array $base, string $settingName, $settings, string $scopePath) : array {
        foreach ($settings as $name => $value) {
            $base[$settingName . '.' . $name] = $scopePath;
        }

        return $base;
    }
}