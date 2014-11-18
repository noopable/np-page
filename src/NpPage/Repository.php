<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

use Zend\Stdlib\ArrayUtils;

/**
 * ブロックを取得するRepository
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Repository implements RepositoryInterface
{
    /**
     *
     * @var BlockPluginManager
     */
    protected $blockPluginManager;

    /**
     *
     * @var string
     */
    protected $currentPage;

    /**
     *
     * @var string
     */
    protected $errorPage;

    /**
     *
     * @var string
     */
    protected $renderErrorPage;

    /**
     *
     * @var array
     */
    protected $pages;

    /**
     *
     * @var array
     */
    protected $blockConfig = array();

    protected $configResolver;

    protected $configLoader;

    public function setBlockPluginManager(BlockPluginManager $blockPluginManager = null)
    {
        $this->blockPluginManager = $blockPluginManager;
    }

    public function getBlockPluginManager()
    {
        return $this->blockPluginManager;
    }

    public function getBlocks()
    {
        return $this->getBlockPluginManager();
    }


    public function getBlock($name, $config = array(), $loadConfig = true)
    {
        if ((false !== $config) && $loadConfig) {
            $this->loadBlockConfig($name);
            $config = ArrayUtils::merge($this->getBlockConfig($name), (array) $config);
        }

        return $this->getBlockPluginManager()->get($name, $config);
    }

    public function getBlockConfig($name)
    {
        $config =  isset($this->blockConfig[$name]) ? $this->blockConfig[$name] : array();
        return $config;
    }

    public function setBlockConfig(array $config, $merge = true)
    {
        if ($merge && count($this->blockConfig)) {
            $config = ArrayUtils::merge($this->blockConfig, $config);
        }
        $this->blockConfig = $config;

        return $this;
    }

    /**
     * @throws Exception\DuplicatedBlockNameException
     * @throws \Zend\Di\Exception\RuntimeException
     * @throws Exception\RuntimeException
     */
    public function getPage($name = null)
    {
        if (null === $name) {
            $name = $this->getCurrentPage();
        }

        if (! isset($this->pages[$name])) {
            $this->pages[$name] = $this->getBlock($name, ['depth' => 0]);
        }

        return $this->pages[$name];
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * エラーが発生するとエラーページに切り替わることがある
     *
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function loadBlockConfig($name, $merge = true)
    {
        //DBからの読み込みを追加・交換する場合、
        //ConfigLoaderを入れ替えるか、EventFulにして追加・交換を可能にする。
        $config = array();
        $configLoader = $this->getConfigLoader();
        if ($configLoader instanceof Config\Loader\ConfigLoaderInterface) {
            $config = $configLoader->load($name);
        }

        if ($merge) {
            $origConfig = $this->getBlockConfig($name);
            $config = ArrayUtils::merge($origConfig, $config);
        }
        $config['name'] = $name;
        $this->setBlockConfig(array($name => $config));
        return $config;
    }

    public function setConfigLoader(Config\Loader\ConfigLoaderInterface $configLoader)
    {
        $this->configLoader = $configLoader;
    }

    public function getConfigLoader()
    {
        if (!isset($this->configLoader)) {
            throw new Exception\RuntimeException('Config Loader is not set');
        }
        return $this->configLoader;
    }

    /**
     *
     * @param string $errorPage
     */
    public function setErrorPage($errorPage)
    {
        $this->errorPage = $errorPage;
    }

    /**
     *
     * @return string
     */
    public function getErrorPage()
    {
        return $this->errorPage;
    }

    /**
     *
     * @param string $renderErrorPage
     */
    public function setRenderErrorPage($renderErrorPage)
    {
        $this->renderErrorPage = $renderErrorPage;
    }

    /**
     *
     * @return string
     */
    public function getRenderErrorPage()
    {
        return $this->renderErrorPage;
    }
}
