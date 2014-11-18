<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Service;

use NpPage\Config\ConfigFileResolver;
use NpPage\ProvidesConfigId;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Resolver\TemplatePathStack;

/**
 * ConfigFileResolverFactory
 *
 * @author tomoaki
 */
class ConfigFileResolverFactory implements FactoryInterface {
    use ProvidesConfigId;
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configResolver = null;
        $config = $serviceLocator->get('Config')[$this->getConfigId()];
        if (isset($config['config_resolver'])) {
            $resolveConf = $config['config_resolver'];
            $configResolver = new ConfigFileResolver();
            if (isset($resolveConf['map'])) {
                $mapResolver =  new TemplateMapResolver($resolveConf['map']);
                $configResolver->attach($mapResolver);
            }

            if (isset($resolveConf['path_stack'])) {
                $stackResolver = new TemplatePathStack();
                $stackResolver->setDefaultSuffix('php');
                $stackResolver->addPaths($resolveConf['path_stack']);
                $configResolver->attach($stackResolver);
            }
        }
        return $configResolver;
    }
}
