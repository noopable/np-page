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

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-12 at 17:51:07.
 */
class BlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Block
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Block;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers NpPage\Block\Block::setBlockBuilder
     */
    public function testSetBlockBuilder()
    {
        $mock = $this->getMock('NpPage\\Builder\BlockBuilderInterface');
        $this->object->setBlockBuilder($mock);
        $this->assertSame($mock, TestTool::getPropertyValue($this->object, 'blockBuilder'));
    }

    /**
     * @covers NpPage\Block\Block::getBlockBuilder
     */
    public function testGetBlockBuilder()
    {
        $mock = $this->getMock('NpPage\\Builder\BlockBuilderInterface');
        $this->object->setBlockBuilder($mock);
        $this->assertSame($mock, $this->object->getBlockBuilder());
    }

    /**
     * @covers NpPage\Block\Block::init
     */
    public function testInitTryToInstantiateBuilder()
    {
        $serviceLocator = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $initializer = $this->getMock('NpPage\\BlockInitializer');
        $initializer->expects($this->once())
                ->method('getServiceLocator')
                ->will($this->returnValue($serviceLocator));
        $this->object->init($initializer);
        $builder = $this->object->getBlockBuilder();
        $this->assertInstanceOf('NpPage\Builder\BlockBuilder', $builder);
        $this->assertSame($serviceLocator, $builder->getServiceLocator());
    }

    /**
     * @covers NpPage\Block\Block::build
     */
    public function testBuild()
    {
        $builder = $this->getMock('NpPage\\Builder\BlockBuilderInterface');
        $builder->expects($this->once())
                ->method('build')
                ->with($this->object);

        $this->object->setBlockBuilder($builder);
        $this->object->build();
    }

    /**
     * @covers NpPage\Block\Block::build
     */
    public function testBuildWithoutBlockBuilder()
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
     * @covers NpPage\Block\Block::__sleep
     */
    public function test__sleep()
    {
        $builder = $this->getMock('NpPage\\Builder\BlockBuilderInterface');
        $this->object->setBlockBuilder($builder);
        $zombie = unserialize(serialize($this->object));
        $this->assertNull(TestTool::getPropertyValue($zombie, 'blockBuilder'));
    }
}
