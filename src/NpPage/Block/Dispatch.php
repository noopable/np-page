<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Block;

use Flower\DispatcherTrait;
use NpPage\BlockInitializer;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Dispatch　viewModelをコントローラーから取得するブロック
 *
 * @author tomoaki
 *
 */
class Dispatch extends Block implements ListenerAggregateInterface
{
    use DispatcherTrait;
    use FetchActionResultTrait;

    public $builderClass = 'NpPage\\Builder\\BlockBuilder';

    /**
     *
     * @var BlockBuilderInterface
     */
    protected $blockBuilder;

    /**
     * configure this block
     * @see ProvidesResource
     *
     * @param array $config
     * @return void
     */
    public function configure(array $config)
    {
        parent::configure($config);
        //dispatchオプションと signatureでうけて、fetchSignatureにセットする。
        $state = $this->getState();

        foreach ($config as $k => $v) {
            switch ($k) {
                case 'controller':
                case 'controller_name':
                    $this->setControllerName($v);
                    break;
                case 'dispatch_options':
                case 'dispatch':
                    $this->setDispatchOptions($v);
                    break;
                case 'signature':
                    $this->setSignature($v);
                    break;
            }
        }
        $state->setFlag($state::CONFIGURED);
    }

    protected function _init(BlockInitializer $blockInitializer)
    {
        parent::_init($blockInitializer);
        $blockInitializer->listen($this);
    }

    public function build()
    {
        $blockBuilder = $this->getBlockBuilder();
        if ($blockBuilder instanceof BlockBuilderInterface) {
            $blockBuilder->build($this);
        } else {
            parent::build();
        }
    }

    public function postDispatch(MvcEvent $e)
    {
        $res = $this->fetchActionResult($e);

        if ($res instanceof ViewModel) {
            return $res;
        }

        //ViewModelが取得できていなければ、dispatchする。
        $res = $this->dispatch();

        if ($res instanceof ViewModel) {
            $this->populateViewModel($res);
            return $res;
        }
    }

    public function __sleep()
    {
        /**
         * @WIP
         *
         * @var array $suppressed
         */
        $suppressed = array(
            'blockBuilder',
            'serviceLocator',
            'state',
        );
        return array_diff(array_keys(get_object_vars($this)), $suppressed);
    }
}