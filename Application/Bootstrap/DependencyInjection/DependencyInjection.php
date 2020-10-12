<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 14-12-2019 11:04
 */

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

global $containerBuilder;
$containerBuilder = new Henri\Framework\Kernel\ContainerService\ContainerService();
$loader = new YamlFileLoader($containerBuilder, new FileLocator(INCLUDE_DIR));
$loader->load('services.yaml');
$containerBuilder->compile();