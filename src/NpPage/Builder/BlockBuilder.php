<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Builder;

use NpPage\Block\BlockInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * 最もシンプルな BlockBuilder
 *
 * @author tomoaki
 */
class BlockBuilder implements BlockBuilderInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $block;

    protected $buildOptions = array();

    /**
     *
     * @var \Zend\Mvc\Router\RouteMatch
     */
    protected $originalRouteMatch;

    public function build(BlockInterface $block)
    {
        $this->block = $block;

        $state = $block->getState();
        if ($state->checkFlag($state::BUILT)) {
            return $block;
        }

        $this->buildOptions = $block->getOption('builder', array());

        $this->_build();

        $this->prepareViewModel($this->block);

        $state->setFlag($state::BUILT);
        return $this->block;
    }

    protected function _build()
    {
        if (is_callable($this->buildOptions)) {
            $res = call_user_func($this->buildOptions, $this);
            if ($res instanceof BlockInterface) {
                $this->block = $block = $res;
            }
        }
    }

    public function prepareViewModel(BlockInterface $block)
    {
        $model = $block->getViewModel();

        $model->setTemplate($block->getTemplate());

        $captureTo = $block->getOption('captureTo', null);
        if (null !== $captureTo) {
            $model->setCaptureTo($captureTo);
        }

        $append = $block->getOption('viewModelAppend', null);
        if (null !== $append) {
            $model->setAppend($append);
        }
    }

    public function getBuildOptions()
    {
        return $this->buildOptions;
    }

    public function thisBlock()
    {
        return $this->block;
    }

    /**
     * callbackでオリジナルリクエストの情報がほしい場合
     *
     * @return \Zend\Mvc\Router\RouteMatch|null
     */
    public function getOriginalRouteMatch()
    {
        if (!isset($this->originalRouteMatch)) {
            $sl = $this->getServiceLocator();
            if (null === $sl) {
                return;
            }
            try {
                $application = $sl->get('Application');
            } catch (\Zend\ServiceManager\ServiceNotFoundException $ex) {
                //logging?
                return;
            }
            $this->originalRouteMatch = $application->getMvcEvent()->getRouteMatch();
        }
        return $this->originalRouteMatch;
    }
}
