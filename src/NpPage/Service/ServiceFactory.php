<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Service;

use NpPage\Service as MediatorService;
use NpPage\ProvidesConfigId;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Pageモジュールがアプリケーション内で動作するための上層になるServiceのFactory
 *
 * @author tomoaki
 */
class ServiceFactory  implements FactoryInterface {
    use ProvidesConfigId;
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')[$this->getConfigId()];

        $service = new MediatorService;
        if ($service instanceof ServiceLocatorAwareInterface) {
            $service->setServiceLocator($serviceLocator);
        }

        $repository = $serviceLocator->get($config['repository_service_name']);
        $service->setRepository($repository);

        return $service;
    }
}
