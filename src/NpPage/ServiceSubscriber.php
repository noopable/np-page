<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * 本Subscriberは、ServiceInterfaceへの依存ではなく、
 * Serviceクラスそのものへの依存である。
 * 　Serviceクラスの(ほぼ)内部クラスという位置づけとなる。
 *
 * @author tomoaki
 */
class ServiceSubscriber implements ListenerAggregateInterface {

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     *
     * @var \NpPage\Service
     */
    protected $service;

    protected $sharedEventAttached;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this->service, 'onRoute'), $priority);
        /**
         *
         * @see \Zend\Mvc\View\Http\ExceptionStrategy
         *         $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'prepareExceptionViewModel'));
         *         $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'prepareExceptionViewModel'));
         *
         * ExceptionStrategyは例外処理に関して、例外処理用のテンプレートを使って、
         * 例外を含むViewModelを準備する。
         *
         * @see \Zend\Mvc\View\HttpViewManager
         *         $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($injectViewModelListener, 'injectViewModel'), -100);
         *         $events->attach(MvcEvent::EVENT_RENDER_ERROR, array($injectViewModelListener, 'injectViewModel'), -100);
         *
         * injectViewModelで行われるのは、EventResultがViewModelなら、そのViewModelの子モデルを削除して、
         * EventのViewModelにappendする。
         *
         *
         * Pageモジュールは、EventのViewModelを置き換えており、自前でinjectできる。
         * メインのエラー処理部分は、ExceptionStrategyに任せる。
         * もし、ExceptionStrategyを変更したい場合はそちらを別のクラスに置き換える。
         *
         * prepareExceptionViewModelで用意されたViewModelをgetResultから取得し、
         * それを、PageのViewModelに適宜追加した後、PageのViewModelをterminateし、
         * resultとしてセットする。
         *
         *
         * Pageモジュールが行うページブロックは、例外処理以外の部分での修飾を
         * 付け加える。
         * MvcEvent::ViewModelに対して、あらかじめ、エラーのおきにくいViewModel
         * をセットする。
         * $contentに対して、ExceptionStrategyによるレンダリング結果が抽入される。
         *
         *
         */
        //dispatchエラー
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this->service, 'onDispatchError'), -50); //preError
        //renderエラー
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, array($this->service, 'onRenderError'), -50);
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * 後学のためとっておきます
     *
     * @param EventManagerInterface $events
     * @param booean $force
     * @return boolean
     */
    public function utilizeSharedEvent(EventManagerInterface $events, $force = false)
    {
        if ($this->sharedEventAttached && (!$force)) {
            return true;
        }

        $eventDef = $this->getSharedEventDefinition();
        if ($eventDef) {
            $sharedEventManager = $events->getSharedManager();
            $eventDefDefault = array(
                'id' => 'Zend\Stdlib\DispatchableInterface',
                'event' => MvcEvent::EVENT_DISPATCH,
                'callback' => function ($e) {return;},
                'priority' => -85
            );
            foreach ($eventDef as $def) {
                $id = isset($def['id']) ? $def['id'] : $eventDefDefault['id'];
                $event = isset($def['event']) ? $def['event'] : $eventDefDefault['event'];
                $callback = isset($def['callback']) ? $def['callback'] : $eventDefDefault['callback'];
                $priority = intval(isset($def['priority']) ? $def['priority'] : $eventDefDefault['priority']);
                $sharedEventManager->attach($id, $event, $callback, $priority);
            }
            $this->sharedEventAttached = true;
        }
    }


    protected function getSharedEventDefinition()
    {
        $def = array(
            /*
            array(
                'callback' => array($this->service->getResultPool(), 'fetchResultViewModel'),
                'priority' => -95,
            )
             *
             */
        );
        return $def;
    }
}
