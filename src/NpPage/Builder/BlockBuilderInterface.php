<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Builder;

use NpPage\Block\BlockInterface;

/**
 * ブロック
 */
interface BlockBuilderInterface
{

    public function build(BlockInterface $block);

    public function prepareViewModel(BlockInterface $block);

    public function getBuildOptions();
    /**
     *
     * @return BlockInterface
     */
    public function thisBlock();
}