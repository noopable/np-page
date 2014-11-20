<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Builder;

use NpPage\Exception\RuntimeException;
use NpPage\Block\BlockInterface;
use NpPage\Block\BlockContainerInterface;

/**
 * Container Builder
 *
 * @author tomoaki
 */
class ContainerBuilder extends BlockBuilder {

    protected $repository;

    protected $repositoryServiceName = 'NpPage_BlockRepository';

    /**
     *
     * @param \Page\Block\BlockInterface $block
     * @return \Page\Block\BlockInterface
     * @throws Exception\RuntimeException
     */
    public function build(BlockInterface $block){
        if (! $block instanceof BlockContainerInterface) {
            throw new Exception\RuntimeException(__CLASS__ . ' depends on \\NpPage\\Block\\BlockContainerInterface');
        }

        return parent::build($block);
    }

    protected function _build()
    {
        parent::_build();

        if (is_array($this->buildOptions) && isset($this->buildOptions['blocks'])) {
            $this->addChildrenWithDefinition($this->block, $this->buildOptions['blocks']);
        }

        $arrayConfig = $this->block->getBlockArrayConfig();
        if (is_array($arrayConfig) && !empty($arrayConfig)) {
            $this->addChildrenWithDefinition($this->block, $arrayConfig);
        }
    }

    public function addChildrenWithDefinition(BlockContainerInterface $parent, array $def)
    {
        foreach ($def as $k => $v) {
            $name   = 'block';
            $config = array();
            $load   = false;
            if (is_array($v)) {
                if (isset($v['name'])) {
                    $name = $v['name'];
                } elseif (is_string($k)) {
                    $name = $v['name'] = $k;
                }

                if (isset($v['config'])) {
                    $config = $v['config'];
                    $config['name'] = $name;
                } else {
                    $config = $v;
                }

                if (isset($v['load'])) {
                    $load = (bool) $v['load'];
                }
            } elseif (is_string($v)) {
                $name = $v;
                $load = true;
            }

            $child = $this->getRepository()->getBlock($name, $config, $load);
            $parent->insertBlock($child);
        }

        return $parent;
    }

    public function load($name)
    {
        $this->getRepository()->loadBlockConfig($name);
    }

    /**
     * Callbackから利用する
     *
     * @param type $name
     * @param type $config
     * @return type
     */
    public function get($name, $config = array())
    {
        return $this->getRepository()->getBlock($name, $config);
    }

    public function setInvokable($name, $class)
    {
        $this->getRepository()->getBlocks()->setInvokable($name, $class);
        return $this;
    }

    public function simple($name, $config = array(), $load = true)
    {
        if (! $this->block instanceof BlockContainerInterface) {
            throw new RuntimeException('This block is not container. Do not use "block" in config');
        }

        $config['class'] = 'simple';
        $block = $this->getRepository()->getBlock($name, $config, $load);
        $this->block->insertBlock($block);
        return $this;
    }

    public function block($name, $config = array(), $load = true)
    {
        if (! $this->block instanceof BlockContainerInterface) {
            throw new RuntimeException('This block is not container. Do not use "block" in config');
        }

        $config['class'] = 'block';
        $block = $this->getRepository()->getBlock($name, $config, $load);
        $this->block->insertBlock($block);
        return $this;
    }

    /**
     * 引数の順序はあまり問題ではない。
     *
     * @throws RuntimeException
     */
    public function insert()
    {
        if (! $this->block instanceof BlockContainerInterface) {
            throw new RuntimeException('This block is not container. Do not use "insert" in config');
        }

        $args = func_get_args();
        $config = array();
        $load = true;
        foreach ($args as $arg) {
            if (is_string($arg)) {
                $name = $arg;
            }

            if (is_array($arg)) {
                $config = $arg;
            }

            if (is_bool($arg)) {
                $load = $arg;
            }

            if (is_object($arg)
                && $arg instanceof BlockInterface) {
                $this->block->insertBlock($arg);
                continue;
            }
        }

        if (!isset($name)) {
            if (isset($config['name'])) {
                $name = $config['name'];
            } else {
                $name = uniqid('block_');
                if (! isset($config['class'])) {
                    $config['class'] = 'block';
                }
            }
        }

        /**
         * $config は$block->configure()の引数として渡される。
         * $config['name'] $config['invokable']などでBlockPluginManagerの動作を変更できる。
         */
        $block = $this->getRepository()->getBlock($name, $config, $load);
        $this->block->insertBlock($block);

        return $this;
    }

    public function setRepository($repository = null)
    {
        if (null === $repository) {
            if ($this->getServiceLocator()->has($this->repositoryServiceName)) {
                $repository = $this->getServiceLocator()->get($this->repositoryServiceName);
            }
        }
        $this->repository = $repository;
    }

    public function getRepository()
    {
        if (!isset($this->repository)) {
            $this->setRepository();
        }
        return $this->repository;
    }
}
