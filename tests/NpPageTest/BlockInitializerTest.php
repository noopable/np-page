<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPageTest;

use Flower\Test\TestTool;
use NpPage\Block\Block;
use NpPage\BlockInitializer;
use Zend\ServiceManager\ServiceManager;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1
 */
class BlockInitializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockInitializer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new BlockInitializer;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers NpPage\BlockInitializer::setCreationOptions
     */
    public function testSetCreationOptions()
    {
        $options = array(
            'foo' => 'bar',
        );
        $this->object->setCreationOptions($options);
        $this->assertEquals($options, TestTool::getPropertyValue($this->object, 'creationOptions'));
    }

    /**
     * @covers NpPage\BlockInitializer::initialize
     */
    public function testInitialize()
    {
        $block = new Block;
        $options = array('template' => 'foo');
        $this->object->setCreationOptions($options);
        $this->object->initialize($block, new ServiceManager);
        $this->assertEquals('foo', $block->getTemplate());
        $this->assertEmpty(TestTool::getPropertyValue($this->object, 'creationOptions'));
        $this->assertEmpty(TestTool::getPropertyValue($this->object, 'inProcess')->count());
        $state = $block->getState();
        $this->assertTrue($state->checkFlag($state::INITIALIZED));
        $this->assertTrue($state->checkFlag($state::BUILT));
    }

    /**
     * @covers NpPage\BlockInitializer::listen
     */
    public function testListen()
    {
        $block = $this->getMock('Zend\EventManager\ListenerAggregateInterface');
        $eventManager = $this->getMock('Zend\EventManager\EventManager');
        $eventManager->expects($this->once())
                ->method('AttachAggregate')
                ->with($this->identicalTo($block));
        $serviceLocator = new ServiceManager;
        $serviceLocator->setService('EventManager', $eventManager);
        $this->object->setServiceLocator($serviceLocator);
        $this->object->listen($block);
    }
}