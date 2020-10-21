<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 14-12-2019 11:04
 */

namespace Henri\Application\Bootstrap\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Henri\Framework\Kernel\ContainerService\ContainerService;

class DependencyInjection {

    /**
     * Initialize DI
     * 
     * @throws Exception
     */
    public function initialize(): void {
        global $containerBuilder;
        $containerBuilder = new ContainerService();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(INCLUDE_DIR));
        $loader->load('services.yaml');
        $containerBuilder->compile();
    }
    
}