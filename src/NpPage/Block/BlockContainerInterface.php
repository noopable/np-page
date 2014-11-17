<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Block;

/**
 *
 * @author tomoaki
 */
interface BlockContainerInterface  extends BlockInterface {
    public function setBlockArrayConfig(array $blockArrayConfig);

    /**
     *
     * @return array
     */
    public function getBlockArrayConfig();
    
    /**
     *
     * @param string $name
     * @return null|BlockInterface
     */
    public function digByName($name);

    public function byName($name = null);

    public function insertBlock(BlockInterface $block);

    public function removeBlock(BlockInterface $block);

}

