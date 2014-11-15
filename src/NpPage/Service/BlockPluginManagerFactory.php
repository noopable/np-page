<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Service;

use NpPage\BlockInitializer;
use NpPage\BlockPluginManager;
use NpPage\Exception\RuntimeException;
use NpPage\ProvidesConfigId;
use Zend\ServiceManager\Config as ServiceManagerConfig;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\Di\DiServiceInitializer;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * Description of BlockPluginManagerFactory
 *
 * @author tomoaki
 */
class BlockPluginManagerFactory implements FactoryInterface {
    use ProvidesConfigId;
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

        if (isset($config['block_plugin'])) {
            $blockPlugin = $config['block_plugin'];
        } else {
            $blockPlugin = array();
        }

        if (is_array($blockPlugin)) {
            $managerConfig = new ServiceManagerConfig($blockPlugin);
            $blockPlugin = new BlockPluginManager($managerConfig);
        }

        if ($blockPlugin instanceof BlockPluginManager) {
            $initializer = new BlockInitializer;
            $initializer->setServiceLocator($serviceLocator);
            $blockPlugin->setBlockInitializer($initializer);
        }

        if ($serviceLocator->has('Di')) {
            $di = $serviceLocator->get('Di');
            $blockPlugin->addAbstractFactory(
                //new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_BEFORE_DI)
                new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_NONE)
            );
            $blockPlugin->addInitializer(
                new DiServiceInitializer($di, $serviceLocator)
            );
        }
        return $blockPlugin;
    }
}
