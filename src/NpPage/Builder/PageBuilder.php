<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Builder;

use NpPage\Block\BlockInterface;

/**
 * PageBuilder
 *
 * @author tomoaki
 */
class PageBuilder extends BlockBuilder {
    public function prepareViewModel(BlockInterface $block)
    {
        $sl = $this->getServiceLocator();
        $viewModel = $block->getViewModel();

        if (! $block->getTemplate()) {
            $layout = $sl->get('ViewManager')->getLayoutTemplate();
            $block->setTemplate($layout);
            $viewModel->setTemplate($layout);
        }

        $viewModel->setTerminal(true);
        $sl->get('Application')->getMvcEvent()->setViewModel($viewModel);
    }
}
