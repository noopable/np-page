<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Block;

use ArrayAccess;
use NpPage\BlockInitializer;
use NpPage\ProvidesResource;
use NpPage\State;
use Zend\View\Model\ViewModel;

/**
 * ドキュメントを表現するblockオブジェクト
 *
 * ArrayAccessを残す利便性があるかどうか。
 *
 * @author tomoaki
 *
 */
class Simple implements BlockInterface, ArrayAccess
{
    use ProvidesResource;

    public $viewModel;

    protected $template;

    /**
     * block priority in parent block
     *
     * @var int
     */
    public $priority;

    /**
     *
     * @var \NpPage\State
     */
    protected $state;

    /**
     * configure this block
     * @see ProvidesResource
     *
     * @param array $config
     * @return void
     */
    public function configure(array $config)
    {
        $this->configureResource($config);
        //For BC
        if (isset($config['order'])) {
            $this->setPriority((int) $config['order']);
        }

        if (isset($config['priority'])) {
            $this->setPriority((int) $config['priority']);
        }

        if (isset($config['template'])) {
            $this->setTemplate((string) $config['template']);
        }

        $state = $this->getState();
        $state->setFlag($state::CONFIGURED);
    }

    public function init(BlockInitializer $blockInitializer)
    {
        $this->_init($blockInitializer);
        $state = $this->getState();
        if (! $state->checkFlag($state::BUILT)) {
            $this->build();
        }
        $state->setFlag($state::INITIALIZED);
    }

    protected function _init(BlockInitializer $blockInitializer)
    {
    }

    /**
     *
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * 名称はtemplateが実質的にviewScriptのパス
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * templateが設定されていないときのケアはサービス側で行う。
     * リソースは感知しない。
     *
     * @return string
     */
    public function getTemplate()
    {
        if (isset($this->template)) {
            return $this->template;
        }
        return $this->getOption('template', null);
    }

    public function setViewModel(ViewModel $viewModel = null)
    {
        if (null === $viewModel) {
            $viewModel = new ViewModel;
            $template = $this->getTemplate();
            if (strlen($template)) {
                $viewModel->setTemplate($template);
            }
        } else {
            $variables = $viewModel->getVariables();
            if (isset($variables)) {
                $this->setProperties($variables);
            }
        }

        $viewModel->setVariables($this->getProperties(), true);
        $this->viewModel = $viewModel;

        return $this;
    }

    public function getViewModel()
    {
        if (!isset($this->viewModel)) {
            $this->setViewModel();
        }
        return $this->viewModel;
    }

    public function getState()
    {
        if (!isset($this->state)) {
            $this->state = new State;
        }
        return $this->state;
    }

    public function build()
    {
        $state = $this->getState();

        $model = $this->getViewModel();

        $model->setTemplate($this->getTemplate());

        if ($captureTo = $this->getOption('captureTo', false)) {
            $model->setCaptureTo($captureTo);
        }

        if ($append = $this->getOption('viewModelAppend', null)) {
            $model->setAppend($append);
        }

        $state->setFlag($state::BUILT);
    }

    public function offsetExists($offset)
    {
        return $this->issetProperty($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getProperty($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->setProperty($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->setProperty($offset, null);
        return $this;
    }

    public function __sleep()
    {
        /**
         *
         * @var array $suppressed
         */
        $suppressed = array(
            'state',
            'viewModel',
        );

        return array_diff(array_keys(get_object_vars($this)), $suppressed);
    }
}