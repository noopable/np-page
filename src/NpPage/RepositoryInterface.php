<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface RepositoryInterface
{

    public function getPage($name = null);
    public function setCurrentPage($currentPage);
    public function getCurrentPage();

    public function setBlockConfig(array $config, $merge = true);
    public function loadBlockConfig($name, $merge = true);
    public function getBlockConfig($name);

    public function getBlock($name, $config = array(), $loadConfig = true);


    /**
     * @alias getBlockPluginManager
     * @return BlockPluginManager
     */
    public function getBlocks();

    /**
     *
     * @return BlockPluginManager
     */
    public function getBlockPluginManager();
    public function setBlockPluginManager(BlockPluginManager $blockPluginManager = null);
}
