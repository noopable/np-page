<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPageTest;

use Flower\Test\TestTool;
use NpPage\Repository;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-18 at 20:16:34.
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Repository
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Repository;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers NpPage\Repository::setBlockPluginManager
     */
    public function testSetBlockPluginManager()
    {
        $blockPluginManager = $this->getMock('NpPage\\BlockPluginManager');
        $this->object->setBlockPluginManager($blockPluginManager);
        $this->assertSame($blockPluginManager, TestTool::getPropertyValue($this->object, 'blockPluginManager'));
    }

    /**
     * @covers NpPage\Repository::getBlockPluginManager
     */
    public function testGetBlockPluginManager()
    {
        $blockPluginManager = $this->getMock('NpPage\\BlockPluginManager');
        $this->object->setBlockPluginManager($blockPluginManager);
        $this->assertSame($blockPluginManager, $this->object->getBlockPluginManager());
    }

    /**
     * @covers NpPage\Repository::getBlocks
     */
    public function testGetBlocks()
    {
        $blockPluginManager = $this->getMock('NpPage\\BlockPluginManager');
        $this->object->setBlockPluginManager($blockPluginManager);
        $this->assertSame($blockPluginManager, $this->object->getBlocks());
    }

    /**
     * @covers NpPage\Repository::getBlock
     */
    public function testGetBlockWithoutLoad()
    {
        $name = 'foo';
        $config = array('a' => 'A');
        $block = $this->getMock('NpPage\\Block\\BlockInterface');
        $blockPluginManager = $this->getMock('NpPage\\BlockPluginManager');
        $blockPluginManager->expects($this->once())
                ->method('get')
                ->with($this->equalTo($name), $this->equalTo($config))
                ->will($this->returnValue($block));
        $this->object->setBlockPluginManager($blockPluginManager);
        $res = $this->object->getBlock($name, $config, false);
        $this->assertSame($block, $res);
    }

    /**
     * @covers NpPage\Repository::getBlock
     */
    public function testGetBlockWithLoad()
    {
        $name = 'foo';
        $config = array('a' => 'A');
        $mergedConfig =  Array (
            'a' => 'A',
            'name' => 'foo'
        );
        $block = $this->getMock('NpPage\\Block\\BlockInterface');
        $blockPluginManager = $this->getMock('NpPage\\BlockPluginManager');
        $blockPluginManager->expects($this->once())
                ->method('get')
                ->with($this->equalTo($name), $this->equalTo($mergedConfig))
                ->will($this->returnValue($block));
        $this->object->setBlockPluginManager($blockPluginManager);
        $configLoader = $this->getMock('NpPage\\Config\\Loader\\ConfigLoaderInterface');
        $configLoader->expects($this->once())
            ->method('load')
            ->with($this->equalTo($name))
            ->will($this->returnValue($config));
        $this->object->setConfigLoader($configLoader);
        $res = $this->object->getBlock($name, $config);
        $this->assertSame($block, $res);
    }

    /**
     * @covers NpPage\Repository::setConfigLoader
     */
    public function testSetConfigLoader()
    {
        $configLoader = $this->getMock('NpPage\\Config\\Loader\\ConfigLoaderInterface');
        $this->object->setConfigLoader($configLoader);
        $this->assertSame($configLoader, TestTool::getPropertyValue($this->object, 'configLoader'));
    }

    /**
     * @covers NpPage\Repository::getConfigLoader
     */
    public function testGetConfigLoader()
    {
        $configLoader = $this->getMock('NpPage\\Config\\Loader\\ConfigLoaderInterface');
        $this->object->setConfigLoader($configLoader);
        $this->assertSame($configLoader, $this->object->getConfigLoader());
    }

    /**
     * @covers NpPage\Repository::setBlockConfig
     */
    public function testSetBlockConfig()
    {
        $config = array('a' => 'A');
        $this->object->setBlockConfig($config);
        $this->assertEquals($config, TestTool::getPropertyValue($this->object, 'blockConfig'));
    }


    /**
     * @depends testSetBlockConfig
     * @covers NpPage\Repository::getBlockConfig
     */
    public function testGetBlockConfig()
    {
        $bConfig = array('b' => 'B');
        $config = array(
            'b' => $bConfig,
        );
        $this->object->setBlockConfig($config);
        $this->assertEquals($bConfig, $this->object->getBlockConfig('b'));
    }

    /**
     * @covers NpPage\Repository::loadBlockConfig
     */
    public function testLoadBlockConfig()
    {
        $name = 'b';
        $bConfig1 = array('b1' => 'B1');
        $bConfig2 = array('b2' => 'B2');
        $config = array(
            'b' => $bConfig1,
        );
        $this->object->setBlockConfig($config);
        $configLoader = $this->getMock('NpPage\\Config\\Loader\\ConfigLoaderInterface');
        $configLoader->expects($this->once())
                ->method('load')
                ->with($this->equalTo($name))
                ->will($this->returnValue($bConfig2));
        $this->object->setConfigLoader($configLoader);
        $res = $this->object->loadBlockConfig($name);
        $this->assertEquals(array('b1' => 'B1', 'b2' => 'B2', 'name' => 'b'), $res);
        $this->assertEquals($res, $this->object->getBlockConfig($name));
    }

    /**
     * @covers NpPage\Repository::getPage
     */
    public function testGetPageWithNameStockedPage()
    {
        $name = 'foo';
        $page = $this->getMock('NpPage\\Page');
        $ref = new \ReflectionObject($this->object);
        $prop = $ref->getProperty('pages');
        $prop->setAccessible(true);
        $prop->setValue($this->object, array('foo' => $page));

        $res = $this->object->getPage($name);
        $this->assertSame($page, $res);
    }

    /**
     * @covers NpPage\Repository::getPage
     */
    public function testGetPageWithoutNameStockedPage()
    {
        $name = 'foo';
        $this->object->setCurrentPage($name);
        $page = $this->getMock('NpPage\\Page');
        $ref = new \ReflectionObject($this->object);
        $prop = $ref->getProperty('pages');
        $prop->setAccessible(true);
        $prop->setValue($this->object, array('foo' => $page));

        $res = $this->object->getPage();
        $this->assertSame($page, $res);
    }

    /**
     * @depends testGetBlockWithLoad
     * @covers NpPage\Repository::getPage
     */
    public function testGetPageWithNameWithLoad()
    {
        $name = 'foo';
        $page = $this->getMock('NpPage\\Page');
        $config = array('b' => 'B');
        $mergedConfig = $config;
        $mergedConfig['name'] = $name;
        $mergedConfig['depth'] = 0;
        $blockPluginManager = $this->getMock('NpPage\\BlockPluginManager');
        $blockPluginManager->expects($this->once())
                ->method('get')
                ->with($this->equalTo($name), $this->equalTo($mergedConfig))
                ->will($this->returnValue($page));
        $this->object->setBlockPluginManager($blockPluginManager);
        $configLoader = $this->getMock('NpPage\\Config\\Loader\\ConfigLoaderInterface');
        $configLoader->expects($this->once())
                ->method('load')
                ->with($this->equalTo($name))
                ->will($this->returnValue($config));
        $this->object->setConfigLoader($configLoader);
        $res = $this->object->getPage($name);
        $this->assertSame($page, $res);
    }

    /**
     * @covers NpPage\Repository::setCurrentPage
     */
    public function testSetCurrentPage()
    {
        $currentPage = 'foo';
        $this->object->setCurrentPage($currentPage);
        $this->assertEquals($currentPage, TestTool::getPropertyValue($this->object, 'currentPage'));
    }

    /**
     * @covers NpPage\Repository::getCurrentPage
     */
    public function testGetCurrentPage()
    {
        $currentPage = 'foo';
        $this->object->setCurrentPage($currentPage);
        $this->assertEquals($currentPage, $this->object->getCurrentPage());
    }

    /**
     * @covers NpPage\Repository::setErrorPage
     */
    public function testSetErrorPage()
    {
        $errorPage = 'foo';
        $this->object->setErrorPage($errorPage);
        $this->assertEquals($errorPage, TestTool::getPropertyValue($this->object, 'errorPage'));
    }

    /**
     * @covers NpPage\Repository::getErrorPage
     */
    public function testGetErrorPage()
    {
        $errorPage = 'foo';
        $this->object->setErrorPage($errorPage);
        $this->assertEquals($errorPage, $this->object->getErrorPage());
    }

    /**
     * @covers NpPage\Repository::setRenderErrorPage
     */
    public function testSetRenderErrorPage()
    {
        $errorPage = 'foo';
        $this->object->setRenderErrorPage($errorPage);
        $this->assertEquals($errorPage, TestTool::getPropertyValue($this->object, 'renderErrorPage'));
    }

    /**
     * @covers NpPage\Repository::getRenderErrorPage
     */
    public function testGetRenderErrorPage()
    {
        $errorPage = 'foo';
        $this->object->setRenderErrorPage($errorPage);
        $this->assertEquals($errorPage, $this->object->getRenderErrorPage());
    }
}
