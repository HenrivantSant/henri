<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 13-12-2019 21:45
 */

namespace Henri\Framework\Annotations;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class Annotations {

	/**
	 * @var ContainerService\ContainerService $containerService
	 */
	protected $containerService;

	/**
	 * @var AnnotationReader $annotationReader
	 */
	protected $annotationReader;

	/**
	 * Annotations constructor.
	 *
	 * @param AnnotationReader $annotationReader
	 *
	 * @throws \Exception
	 */
	public function __construct(
			AnnotationReader  $annotationReader
	) {
		global $containerBuilder;
		// Load framework annotations
		$framework_path = __DIR__ . '/Annotation';
		$this->loadAnnotionsInPath($framework_path);
		$this->containerService = $containerBuilder;
		$this->annotationReader = $annotationReader;
	}

	/**
	 * Method to get all methods and their annotations by class tag
	 *
	 * @param string $tag
	 *
	 * @return array
	 */
	public function getMethodAnnotationsByClasstag(string $tag) : array {
		$annotations = array();
		$classes = $this->containerService->getDefinitionsByTag($tag);

		if (empty($classes)) {
			return $annotations;
		}

		foreach ($classes as $class) {
			$classMethodsAnnotations = $this->getMethodsAnnotationsByClass($class);
			if (!$classMethodsAnnotations->methods) {
				continue;
			}

			$annotations[$classMethodsAnnotations->name] = $classMethodsAnnotations;
		}

		return $annotations;
	}

	/**
	 * Method to get all annotations of all methods in the given class
	 *
	 * @param $class
	 *
	 * @return \stdClass
	 * @throws \ReflectionException
	 */
	protected function getMethodsAnnotationsByClass($class) : \stdClass {
		$reflection   = $this->containerService->getReflectionClass($class);
		$methods      = $reflection->getMethods();
		$classMethods = new \stdClass();
		$classMethods->name     = $class;
		$classMethods->methods  = array();

		if (!is_array($methods) || empty($methods)) {
			$classMethods->methods = false;
			return $classMethods;
		}

		foreach ($methods as $method) {
			$method_annotations = $this->annotationReader->getMethodAnnotations($method);
			if (empty($method_annotations)) {
				continue;
			}
			$methodAnnotionsObject              = new \stdClass();
			$methodAnnotionsObject->name        = $method->name;
			$methodAnnotionsObject->annotations = array();
			foreach ($method_annotations as $key => $annotation) {
				$annotationType = get_class($annotation);
				$annotationName = explode('\\', $annotationType);
				$annotationName = end($annotationName);
				$annotationObject = new \stdClass();
				$annotationObject->name = $annotationName;
				$annotationObject->type = $annotationType;
				$annotationObject->vars = array();
				foreach (get_object_vars($annotation) as $var => $value) {
					$annotationObject->vars[$var] = $value;
				}
				$methodAnnotionsObject->annotations[$annotationName] = $annotationObject;
			}

			$classMethods->methods[$methodAnnotionsObject->name] = $methodAnnotionsObject;
		}

		return $classMethods;
	}

	/**
	 * Method to load all annotation in a given file path
	 *
	 * @param string $path
	 *
	 * @throws \Exception
	 */
	public function loadAnnotionsInPath(string $path) : void {
		if (!is_dir($path) || !is_readable($path)) {
			throw new \Exception('Given path ( ' . $path . ' ) does not exist', 500);
		}

		$files = glob($path . '/*');
		if (!is_array($files) || empty($files)) {
			return;
		}

		foreach ($files as $file) {
			if (is_file($file)) {
				// Load the file
				require_once $file;
				AnnotationRegistry::registerFile($file);
			}
		}
	}
}