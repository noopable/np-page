<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

use NpPage\Block\BlockInterface;
use SplObjectStorage;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * BlockInitializer configure block with creation options
 *  and build block
 *
 * @author tomoaki
 */
class BlockInitializer implements InitializerInterface, ServiceLocatorAwareInterface {
    use ServiceLocatorAwareTrait;

    /**
     *
     * @var SplObjectStorage
     */
    protected $inProcess;

    /**
     *
     * @var array
     */
    protected $creationOptions = array();

    /**
     *
     * @var BlockPluginManager
     */
    protected $blocks;

    public function __construct()
    {
        $this->inProcess = new SplObjectStorage();
    }

    /**
     *
     * @param type $options
     */
    public function setCreationOptions($options)
    {
        $this->creationOptions = $options;
    }

    public function initialize($block, ServiceLocatorInterface $serviceLocator){
        $this->setServiceLocator($serviceLocator);
        if (! $block instanceof BlockInterface) {
            throw new Exception\RuntimeException(sprintf(
                'Plugin of type %s is invalid; must implement NpPage\Block\BlockInterface',
                (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
                __NAMESPACE__
            ));
        }

        if ($this->inProcess->offsetExists($block)) {
            throw new Exception\RuntimeException('speicified block is now constructing. a recursion detected. check your block build configuration');
        }

        $this->inProcess->attach($block, true);

        //interfaceを調べて、$this->serviceをinjectする。

        $state = $block->getState();
        try {
            if (! $state->checkFlag($state::CONFIGURED)) {
                //先に、builderにoptionsを渡しておいて、$block->build()で十分か。
                $block->configure($this->creationOptions);
            }
            //子ブロックの生成に影響させない
            $this->creationOptions = array();

            if (! $state->checkFlag($state::INITIALIZED)) {
                $block->init($this);
            }
        } catch (\Exception $e) {
            unset($this->inProcess);
            $this->inProcess = new SplObjectStorage();
            throw $e;
        }

        $this->inProcess->detach($block);
    }

    /**
     * initializeメソッドで設定されるServiceLocatorからEventManagerを
     * 取得できる必要がある。
     *
     *
     * @param ListenerAggregateInterface $object
     * @param type $priority
     */
    public function listen(ListenerAggregateInterface $object, $priority = 1)
    {
        $serviceLocator = $this->getServiceLocator();
        if ($serviceLocator->has('EventManager')) {
            $serviceLocator->get('EventManager')->attachAggregate($object, $priority);
        }
    }
}
