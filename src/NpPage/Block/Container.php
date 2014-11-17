<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Block;

use Flower\RecursivePriorityQueueTrait;
use NpPage\Block\BlockInterface;
use RecursiveIterator;

/**
 * blockを格納できるcontainer
 *
 * @author tomoaki
 *
 */
class Container extends Block implements BlockContainerInterface, RecursiveIterator
{
    use RecursivePriorityQueueTrait;

    public $builderClass = 'NpPage\\Builder\ContainerBuilder';

    /**
     *
     * @var array
     */
    protected $blockArrayConfig;

    protected $items;

    /**
     * configure this block
     * @see ProvidesResource
     *
     * @param array $config
     * @return void
     */
    public function configure(array $config)
    {
        if (isset($config['blocks']) && is_array($config['blocks'])) {
            $this->setBlockArrayConfig($config['blocks']);
        }
        parent::configure($config);
        $state = $this->getState();
        $state->setFlag($state::CONFIGURED);
    }

    public function setBlockArrayConfig(array $blockArrayConfig)
    {
        $this->blockArrayConfig = (array) $blockArrayConfig;
    }

    public function getBlockArrayConfig()
    {
        return $this->blockArrayConfig;
    }

    /**
     *
     * @param string $name
     * @return \NpPage\Block\BlockInterface|null
     */
    public function digByName($name)
    {
        $aName = explode('/', (string) $name, 2);
        $name = array_shift($aName);
        $block = $this->byName($name);

        if (! count($aName)) {
            return $block;
        }

        if (! $block instanceof BlockInterface) {
            return null;
        }

        if ($block instanceof BlockContainerInterface) {
            return $block->digByName(array_shift($aName));
        } else {
            //名前が続いていて一致していないが、子ブロックをエントリを返す
            return $block;
        }
    }

    /**
     *
     * @param string $name
     * @return null|BlockInterface
     */
    public function byName($name = null)
    {
        if (isset($this->items[$name])) {
            return $this->items[$name]['data'];
        }
        return null;
    }

    public function insertBlock(BlockInterface $block)
    {
        $priority = $block->getPriority();
        $name = (string) $block->getName();

        if (strlen($name)) {
            $this->items[$name] = array(
                'data'     => $block,
                'priority' => $priority,
            );
        }

        $this->insert($block, $priority);

        $this->getViewModel()->addChild($block->getViewModel());

        return $this;
    }

    /**
     * ブロックから子ブロックを削除するが、ViewModelは削除されないので注意すること
     *
     * @param BlockInterface $block
     * @return type
     */
    public function removeBlock(BlockInterface $block)
    {
        if (is_string($block)) {
            return $this->removeByName($block);
        }

        $name = $block->getName();
        if (strlen($name)) {
            $this->removeByName($block->getName());
        } else {
            $this->remove($block);
        }
    }

    public function removeByName($name)
    {
        if (isset($this->items[$name])) {
            $datum = $this->items[$name]['data'];
            unset($this->items[$name]);
        }

        return $this->remove($datum);
    }

    /**
     * Does the queue contain the given datum?
     *
     * @param  mixed $datum
     * @return bool
     */
    public function contains($datum)
    {
        foreach ($this->items as $item) {
            if ($item['data'] === $datum) {
                return true;
            }
        }
        return false;
    }

    public function __sleep()
    {
        /**
         * itemsは保持してよい。
         *
         * @var array $suppressed
         */
        $suppressed = array(
            'blockBuilder',
            'service',
            'state',
            'viewModel',
        );
        return array_diff(array_keys(get_object_vars($this)), $suppressed);
    }
}