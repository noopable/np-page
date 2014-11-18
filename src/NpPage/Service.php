<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

use NpPage\Block\BlockInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\View\Model\ViewModel;

/**
 * アプリケーションフローに関与するSubscriber機能
 * 主要オブジェクトを参照できるMediator
 *
 */
class Service implements ServiceInterface, ServiceLocatorAwareInterface
{
    use ProvidesConfigId;
    use ServiceLocatorAwareTrait;

    /**
     *
     * @var bool
     */
    protected $active = false;

    protected $subscriber;

    protected $originalRouteMatch;

    protected $requestedName;

    protected $repository;

    public function activate($requestedName)
    {
        $this->setRequestedName($requestedName);
        try {
            $repository = $this->getRepository();
            $this->active = ($repository->getPage($requestedName) instanceof BlockInterface);
            if ($this->active) {
                $repository->setCurrentPage($requestedName);
            }
        } catch (\Exception $ex) {
            trigger_error($requestedName . ' page load faild:' . $ex->getMessage(), E_USER_WARNING);
            $this->active = false;
            return;
        }
        return $this->active;
    }

    public function isActivated()
    {
        return $this->active;
    }

    public function subscribe(EventManagerInterface $eventManager)
    {
        $eventManager->attachAggregate($this->getSubscriber());
    }

    public function onRoute(MvcEvent $e)
    {
        if ($requestedName = $e->getRouteMatch()->getParam('page', false)) {
            $this->activate($requestedName);
            /**
             *
             * utilizeSharedEventは不要になりました。
             *  $events = $e->getApplication()->getEventManager();
             *  $this->getSubscriber()->utilizeSharedEvent($events);
             *
             */
        }
    }

    /**
     * @see ServiceSubscriber
     * @param MvcEvent $e
     */
    public function onDispatchError(MvcEvent $e)
    {
        if (!$this->isActivated()) {
            //処理対象外
            return;
        }

        $repository = $this->getRepository();

        $repository->setCurrentPage($repository->getErrorPage());
        $pageViewModel = $repository->getPage()->getViewModel();

        $result = $e->getResult();
        if ($result instanceof ViewModel) {
            $pageViewModel->addChild($result);
        }

        $e->setResult($pageViewModel);

    }

    public function onRenderError(MvcEvent $e)
    {
        if (!$this->isActivated()) {
            //処理対象外
            return;
        }

        $repository = $this->getRepository();

        $repository->setCurrentPage($repository->getRenderErrorPage());
        $pageViewModel = $repository->getPage()->getViewModel();

        if ($e->getError() && $pageViewModel instanceof ClearableModelInterface) {
            $pageViewModel->clearChildren();
        }

        $result = $e->getResult();
        if ($result instanceof ViewModel) {
            $pageViewModel->addChild($result);
        }

        $e->setResult($pageViewModel);

    }

    public function setRequestedName($requestedName)
    {
        $this->requestedName = $requestedName;
    }

    public function getRequestedName()
    {
        return $this->requestedName;
    }

    public function setSubscriber(ListenerAggregateInterface $subscriber = null)
    {
        if (null === $subscriber) {
            $subscriber = new ServiceSubscriber($this);
        }
        $this->subscriber = $subscriber;
    }

    public function getSubscriber()
    {
        if (! isset($this->subscriber)) {
            $this->setSubscriber(null);
        }
        return $this->subscriber;
    }

    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }
}
