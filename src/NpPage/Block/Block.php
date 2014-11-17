<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Block;

use NpPage\BlockInitializer;
use NpPage\Builder\BlockBuilderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * ドキュメントを表現するblockオブジェクト
 *
 * @author tomoaki
 *
 */
class Block extends Simple implements BlockInterface
{
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
        $state = $this->getState();
        $state->setFlag($state::CONFIGURED);
    }

    protected function _init(BlockInitializer $blockInitializer)
    {
        $blockBuilder = $this->getBlockBuilder();
        if ($blockBuilder instanceof ServiceLocatorAwareInterface) {
            $blockBuilder->setServiceLocator($blockInitializer->getServiceLocator());
        }
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

    /**
     *
     * @return BlockBuilderInterface
     */
    public function getBlockBuilder()
    {
        if (!isset($this->blockBuilder)) {
            $this->setBlockBuilder();
        }
        return $this->blockBuilder;
    }

    /**
     *
     * @param BlockBuilderInterface $blockBuilder
     */
    public function setBlockBuilder(BlockBuilderInterface $blockBuilder = null)
    {
        if (null === $blockBuilder) {
            $blockBuilder = new $this->builderClass;
        }
        $this->blockBuilder = $blockBuilder;
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