<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPageTest\Block;

use Flower\Test\TestTool;
use NpPage\Block\Simple;
use Zend\View\Model\ViewModel;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-13 at 20:56:30.
 */
class SimpleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Simple
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Simple;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers NpPage\Block\Simple::configure
     */
    public function testConfigure()
    {
        $config = array(
            'priority' => 5,
            'template' => 'foo/bar',
        );
        $this->object->configure($config);
        $this->assertEquals(5, $this->object->getPriority());
        $this->assertEquals('foo/bar', $this->object->getTemplate());
        $state = $this->object->getState();
        $this->assertInstanceOf('NpPage\\State', $state);
    }

    /**
     * @covers NpPage\Block\Simple::init
     */
    public function testInit()
    {
        $state = $this->object->getState();
        $this->assertFalse($state->checkFlag($state::INITIALIZED));
        $mock = $this->getMockBuilder('NpPage\\BlockInitializer')
                ->disableOriginalConstructor()->getMock();
        $this->object->init($mock);
        $this->assertTrue($state->checkFlag($state::INITIALIZED));
    }

    /**
     * @covers NpPage\Block\Simple::setPriority
     */
    public function testSetPriority()
    {
        $this->object->setPriority(10);
        $this->assertEquals(10, TestTool::getPropertyValue($this->object, 'priority'));
    }

    /**
     * @covers NpPage\Block\Simple::getPriority
     */
    public function testGetPriority()
    {
        $this->object->setPriority(10);
        $this->assertEquals(10, $this->object->getPriority());
    }

    /**
     * @covers NpPage\Block\Simple::setTemplate
     */
    public function testSetTemplate()
    {
        $this->object->setTemplate('foo');
        $this->assertEquals('foo', TestTool::getPropertyValue($this->object, 'template'));
    }

    /**
     * @covers NpPage\Block\Simple::getTemplate
     */
    public function testGetTemplate()
    {
        $this->assertNull($this->object->getTemplate());
        $this->object->setOption('template', 'bar');
        $this->assertEquals('bar', $this->object->getTemplate());
        $this->object->setTemplate('foo');
        $this->assertEquals('foo', $this->object->getTemplate());
    }

    /**
     * @covers NpPage\Block\Simple::setViewModel
     */
    public function testSetViewModelNull()
    {
        $this->object->setProperty('foo', 'bar');
        $this->object->setViewModel();

        $this->assertEquals('bar', $this->object->getViewModel()->foo);
    }

    /**
     * @covers NpPage\Block\Simple::setViewModel
     */
    public function testSetViewModel()
    {
        $viewModel = new ViewModel;
        $viewModel->foo = 'bar';
        $this->object->setViewModel($viewModel);
        $this->assertSame($viewModel, TestTool::getPropertyValue($this->object, 'viewModel'));
        $this->assertEquals('bar', $this->object->getProperty('foo'));
    }

    /**
     * @covers NpPage\Block\Simple::getViewModel
     */
    public function testGetViewModel()
    {
        $mock = $this->getMock('Zend\\View\\Model\\ViewModel');
        $this->object->setViewModel($mock);
        $this->assertSame($mock, $this->object->getViewModel());
    }

    /**
     * @covers NpPage\Block\Simple::getState
     */
    public function testGetState()
    {
        $state = $this->object->getState();
        $this->assertInstanceOf('NpPage\\State', $state);
    }

    /**
     * @covers NpPage\Block\Simple::build
     */
    public function testBuild()
    {
        $options = array(
            'template' => 'foo',
            'viewModelAppend' => true,
            'captureTo' => 'core',
        );
        $this->object->setOptions($options);
        $this->object->build();
        $viewModel = $this->object->getViewModel();
        $this->assertEquals('foo', $viewModel->getTemplate());
        $this->assertTrue($viewModel->isAppend());
        $this->assertEquals('core', $viewModel->captureTo());
    }

    /**
     * @covers NpPage\Block\Simple::offsetExists
     */
    public function testOffsetExists()
    {
        $this->object->setProperty('foo', 'bar');
        $this->assertTrue($this->object->offsetExists('foo'));
    }

    /**
     * @covers NpPage\Block\Simple::offsetGet
     */
    public function testOffsetGet()
    {
        $this->object->setProperty('foo', 'bar');
        $this->assertEquals('bar', $this->object->offsetGet('foo'));
        $this->assertEquals('bar', $this->object['foo']);
    }

    /**
     * @covers NpPage\Block\Simple::offsetSet
     */
    public function testOffsetSet()
    {
        $this->object->offsetSet('foo', 'bar');
        $this->assertEquals('bar', $this->object->getProperty('foo'));
    }

    /**
     * @covers NpPage\Block\Simple::offsetUnset
     */
    public function testOffsetUnset()
    {
        $this->object->offsetSet('foo', 'bar');
        $this->object->offsetUnset('foo');
        $this->assertNull($this->object->getProperty('foo'));
    }

    /**
     * @covers NpPage\Block\Simple::__sleep
     */
    public function test__sleep()
    {
        $viewModel = $this->getMock('Zend\\View\\Model\\ViewModel');
        $this->object->setViewModel($viewModel);
        $zombie = unserialize(serialize($this->object));
        $this->assertNull(TestTool::getPropertyValue($zombie, 'viewModel'));
    }
}
