<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Service;

use NpPage\BlockPluginManager;
use NpPage\Config\Loader\ConfigLoaderInterface;
use NpPage\Exception\RuntimeException;
use NpPage\ProvidesConfigId;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * Description of BlockPluginManagerFactory
 *
 * @author tomoaki
 */
class BlockRepositoryFactory implements FactoryInterface {
    use ProvidesConfigId;

    protected $repositoryClass = 'NpPage\\Repository';

    /**
     * Create and return abstract factory seeded by dependency injector
     *
     * Creates and returns an abstract factory seeded by the dependency
     * injector. If the "di" key of the configuration service is set, that
     * sub-array is passed to a DiConfig object and used to configure
     * the DI instance. The DI instance is then used to seed the
     * DiAbstractServiceFactory, which is then registered with the service
     * manager.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Di
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $applicationConfig = $serviceLocator->get('Config');
        if (!isset($applicationConfig[$this->getConfigId()])) {
            throw new RuntimeException($this->getConfigId() . ' config not found');
        }
        $config = $applicationConfig[$this->getConfigId()];

        $repository = new $this->repositoryClass;

        $blockPluginManager = $serviceLocator->get($config['blocks_service_name']);
        if ($blockPluginManager instanceof BlockPluginManager) {
            $repository->setBlockPluginManager($blockPluginManager);
        }

        if (isset($config['blocks']) && is_array($config['blocks'])) {
            $repository->setBlockConfig($config['blocks']);
        }
        if (isset($config['error_page'])) {
            $repository->setErrorPage($config['error_page']);
        }
        if (isset($config['render_error_page'])) {
            $repository->setRenderErrorPage($config['render_error_page']);
        }


        if (isset($config['config_loader_name'])
            && $serviceLocator->has($config['config_loader_name'])
            && (method_exists($repository, 'setConfigLoader'))) {
            $configLoader = $serviceLocator->get($config['config_loader_name']);
            if ($configLoader instanceof ConfigLoaderInterface) {
                $repository->setConfigLoader($configLoader);
            }
        }

        return $repository;
    }
}
