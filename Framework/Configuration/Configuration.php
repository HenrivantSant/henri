<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 6-5-2020 18:36
 */

namespace Henri\Framework\Configuration;

use Henri\Framework\Configuration\Helper\Parser;
use RuntimeException;

class Configuration {

	/**
	 * @var YamlFileLoader $fileLoader
	 */
	private $fileLoader;

	/**
	 * @var Parser $parser
	 */
	private $parser;

	/**
	 * @var \stdClass $database
	 */
	private $database;

	/**
	 * @var \stdClass $routing
	 */
	private $routing;

	/**
	 * @var \stdClass $app
	 */
	private $app;

	/**
	 * @var \stdClass $scoped
	 */
	private $scoped;

	/**
	 * @var \stdClass $general
	 */
	private $general;

    /**
     * @var array $mapping
     */
	private $mapping;

	/**
	 * Configuration constructor.
	 *
	 * @param YamlFileLoader $fileLoader
	 */
	public function __construct(
		YamlFileLoader $fileLoader,
		Parser $parser
	) {
		$this->fileLoader   = $fileLoader;
		$this->parser       = $parser;

		$this->loadConfig();
	}

    /**
     * Load configuration from files
     *
     * @throws \Exception
     */
    private function loadConfig(): void {
        $settings = $this->parser->parseConfig($this->fileLoader->getLoadedFiles());
        $config = $settings['config'];

        if (!isset($config->general->database)) {
            throw new \Exception('Database configuration missing', 500);
        }
        if (!isset($config->general->routing)) {
            throw new \Exception('Routing configuration missing', 500);
        }
        if (!isset($config->general->app)) {
            throw new \Exception('Application configuration missing', 500);
        }

        $this->database     = $config->general->database;
        $this->routing      = $config->general->routing;
        $this->app          = $config->general->app;
        $this->general      = $config->general;
        $this->scoped       = $config->scoped;
        $this->mapping      = $settings['map'];
	}

	/**
	 * Method to get setting
	 *
	 * @param string $settingName
     * @param string|null $scope
	 *
	 * @return string|null
	 */
	public function get(string $settingName, ?string $scope = null): ?string {
	    $scope = is_null($scope) ? '' : $scope;
		if ($scope && !property_exists($this->scoped, $scope)) {
			return null;
		}

		$comparison = $scope ? $this->scoped->{$scope} : $this;

		if (count(explode('.', $settingName)) > 1) {
			// Nested setting
			$nameArray  = explode('.', $settingName);
			if (property_exists($comparison, $nameArray[0])) {
				$setting = $comparison->{$nameArray[0]};
				unset($nameArray[0]);

				foreach ($nameArray as $item) {
					if (property_exists($setting, $item)) {
						$setting = $setting->{$item};
					} else {
						return null;
					}
				}

				return $setting;
			} else {
				return null;
			}
		} else {
            return property_exists($comparison, $settingName) ? $comparison->{$settingName} : null;
		}
	}

    /**
     * Update a setting with given parameters. Only existing settings can be updated. New settings can not be created
     *
     * @param string $settingName
     * @param $value
     * @param string|null $scope
     *
     * @throws RuntimeException
     */
    public function set(string $settingName, $value, ?string $scope = null): void {
        if (is_null($this->get($settingName, $scope))) {
            throw new RuntimeException('Setting undefined settings is not supported');
        }

        $filename = !is_null($scope) ? $this->mapping->scoped[$scope][$settingName] : $this->mapping->general[$settingName];
        $settings = $this->fileLoader->parseFile($filename);
        $split = explode('.', $settingName);

        if (count($split) < 2) {
            return;
        }

        $settings[$split[0]][$split[1]] = $value;

        $this->fileLoader->setFile($filename, $settings);
        
        // Reload config after file change
        $this->loadConfig();
	}

}