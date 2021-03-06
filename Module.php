<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

use Zend\Mvc\MvcEvent;

/**
 * Module startup
 *
 */
class Module
{
    /**
     *
     * @var type string
     */
    protected $errorPage = 'ErrorPage';

    protected $config;

    public function onBootstrap(MvcEvent $e)
    {
        $application    = $e->getApplication();
        $eventManager   = $application->getEventManager();
        $serviceManager = $application->getServiceManager();

        $service = $serviceManager->get('NpPage_Service');
        $service->subscribe($eventManager);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        if (! isset($this->config)) {
            $this->config = include __DIR__ . '/config/module.config.php';
        }

        return $this->config;
    }

    public function getProvides()
    {
        return array(
            __NAMESPACE__ => array(
                'version' => '0.1.0'
            ),
        );
    }
/*
    public function getDependencies()
    {
        return array(
            'AsseticBundle' => array(
                'version' => '>=0.1.0',
                'required' => true
            ),
        );
    }
*/
}
