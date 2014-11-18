<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use NpPage\ProvidesConfigId;

/**
 * Description of ConfigLoaderFactory
 *
 * @author tomoaki
 */
class ConfigLoaderFileFactory implements FactoryInterface {
    use ProvidesConfigId;

    protected $class = 'NpPage\Config\Loader\File';

    /**
     *
     * @param  ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')[$this->getConfigId()];

        $configLoader = new $this->class;

        if (isset($config['config_resolver_name'])
                && $serviceLocator->has($config['config_resolver_name'])) {
            $resolver = $serviceLocator->get($config['config_resolver_name']);
            $configLoader->setResolver($resolver);
        }

        return $configLoader;
    }
}
