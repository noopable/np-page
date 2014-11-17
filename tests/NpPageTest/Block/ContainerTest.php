<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPageTest\Block;

use Flower\Test\TestTool;
use NpPage\Block\Block;
use NpPage\Block\Container;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-15 at 17:16:34.
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Container;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers NpPage\Block\Container::setBlockArrayConfig
     */
    public function testSetBlockArrayConfig()
    {
        $config = array(
            'foo' => 'bar',
        );
        $this->object->setBlockArrayConfig($config);
        $this->assertEquals($config, TestTool::getPropertyValue($this->object, 'blockArrayConfig'));
    }

    /**
     * @covers NpPage\Block\Container::getBlockArrayConfig
     */
    public function testGetBlockArrayConfig()
    {
        $config = array(
            'foo' => 'bar',
        );
        $this->object->setBlockArrayConfig($config);
        $this->assertEquals($config, $this->object->getBlockArrayConfig());
    }

    /**
     * @covers NpPage\Block\Container::configure
     */
    public function testConfigure()
    {
        $blockConfig = array(
            'foo' => 'bar',
        );
        $config = array(
            'blocks' => $blockConfig,
            'template' => 'baz',
        );
        $this->object->configure($config);
        $this->assertEquals($blockConfig, $this->object->getBlockArrayConfig());
        $state = $this->object->getState();
        $this->assertTrue($state->checkFlag($state::CONFIGURED));
    }

    /**
     * @covers NpPage\Block\Container::insertBlock
     */
    public function testInsertBlock()
    {
        $block = new Block;
        $block->setName('aa');
        $this->assertTrue($this->object->isEmpty());
        $this->object->insertBlock($block);
        $this->assertFalse($this->object->isEmpty());
        $this->assertSame($block, TestTool::getPropertyValue($this->object, 'items')['aa']['data']);
        $this->assertSame($block, $this->object->current());
    }

    /**
     * @covers NpPage\Block\Container::removeByName
     */
    public function testRemoveByName()
    {
        $block = new Block;
        $block->setName('aa');
        $this->assertTrue($this->object->isEmpty());
        $this->object->insertBlock($block);
        $this->assertFalse($this->object->isEmpty());
        $this->object->removeByName('aa');
        $this->assertTrue($this->object->isEmpty());
    }

    /**
     * @covers NpPage\Block\Container::removeBlock
     */
    public function testRemoveBlock()
    {
        $block = new Block;
        $block->setName('aa');
        $this->assertTrue($this->object->isEmpty());
        $this->object->insertBlock($block);
        $this->assertFalse($this->object->isEmpty());
        $this->object->removeBlock($block);
        $this->assertTrue($this->object->isEmpty());
        $this->assertFalse(isset(TestTool::getPropertyValue($this->object, 'items')['aa']));
    }

    /**
     * @covers NpPage\Block\Container::contains
     */
    public function testContains()
    {
        $block = new Block;
        $block->setName('aa');
        $this->assertTrue($this->object->isEmpty());
        $this->object->insertBlock($block);
        $this->assertTrue($this->object->contains($block));
    }

    /**
     * @covers NpPage\Block\Container::byName
     */
    public function testByName()
    {
        $block = new Block;
        $block->setName('aa');
        $this->assertTrue($this->object->isEmpty());
        $this->object->insertBlock($block);
        $this->assertSame($block, $this->object->byName('aa'));
    }

    /**
     * @covers NpPage\Block\Container::digByName
     */
    public function testDigByName()
    {
        $block = new Block;
        $block->setName('aa');
        $this->assertTrue($this->object->isEmpty());
        $this->object->insertBlock($block);
        $this->assertSame($block, $this->object->digByName('aa'));
        $this->assertSame($block, $this->object->digByName('aa/bb'));

        $container = new Container;
        $container->setName('c');
        $container->insertBlock($block);
        $this->object->insertBlock($container);
        $this->assertSame($container, $this->object->digByName('c'));
        $this->assertSame($block, $this->object->digByName('c/aa'));

    }

    /**
     * @covers NpPage\Block\Container::__sleep
     */
    public function test__sleep()
    {
        $viewModel = $this->getMock('Zend\\View\\Model\\ViewModel');
        $this->object->setViewModel($viewModel);
        $zombie = unserialize(serialize($this->object));
        $this->assertNull(TestTool::getPropertyValue($zombie, 'viewModel'));
    }
}
