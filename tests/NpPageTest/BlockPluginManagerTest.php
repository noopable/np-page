<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPageTest;

use Flower\Test\TestTool;
use NpPage\BlockInitializer;
use NpPage\BlockPluginManager;
use NpPage\Service;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-15 at 14:21:44.
 */
class BlockPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockPluginManager
     */
    protected $object;

    /**
     * 通常、BlockPluginManagerFactoryから取得される。
     */
    protected function setUp()
    {
        $this->object = new BlockPluginManager;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers NpPage\BlockPluginManager::get
     */
    public function testGet()
    {
        $block = $this->object->get('block');
        $this->assertInstanceOf('NpPage\\Block\\Block', $block);
        $simple = $this->object->get('simple');
        $this->assertInstanceOf('NpPage\\Block\\Simple', $simple);

        $options = array(
            'template' => 'foo',
        );

        //without BlockInitializer
        $block2 = $this->object->get('block', $options);
        $this->assertInstanceOf('NpPage\\Block\\Block', $block2);
        $this->assertNotEquals('foo', $block2->getTemplate());

        $blockInitializer = new BlockInitializer;
        $this->object->setBlockInitializer($blockInitializer);

        $options = array(
            'template' => 'bar',
        );
        //with BlockInitializer
        $block3 = $this->object->get('block', $options);
        $this->assertInstanceOf('NpPage\\Block\\Block', $block3);
        $this->assertEquals('bar', $block3->getTemplate());

        //with direct ClassName
        //auto add Invokable?
        $block4 = $this->object->get('NpPage\\Block\\Block');
        $this->assertInstanceOf('NpPage\\Block\\Block', $block4);

        $options = array(
            'template' => 'baz',
        );
        //with direct ClassName
        //but creation options is not executed
        $block5 = $this->object->get('NpPage\\Block\\Block', $options);
        $this->assertInstanceOf('NpPage\\Block\\Block', $block5);
        $this->assertEquals('baz', $block5->getTemplate());
    }

    /**
     * @covers NpPage\BlockPluginManager::setBlockInitializer
     */
    public function testSetBlockInitializer()
    {
        $blockInitializer = $this->getMockBuilder('NpPage\\BlockInitializer')
            ->disableOriginalConstructor()->getMock();
        $this->object->setBlockInitializer($blockInitializer);
        $this->assertSame($blockInitializer, TestTool::getPropertyValue($this->object, 'blockInitializer'));
    }

    /**
     * @covers NpPage\BlockPluginManager::getBlockInitializer
     */
    public function testGetBlockInitializer()
    {
        $blockInitializer = $this->getMockBuilder('NpPage\\BlockInitializer')
            ->disableOriginalConstructor()->getMock();
        $this->object->setBlockInitializer($blockInitializer);
        $this->assertSame($blockInitializer, $this->object->getBlockInitializer());

    }

    /**
     * @covers NpPage\BlockPluginManager::issetBlockInitializer
     */
    public function testIssetBlockInitializer()
    {
        $this->assertFalse($this->object->issetBlockInitializer());
        $blockInitializer = $this->getMockBuilder('NpPage\\BlockInitializer')
                ->disableOriginalConstructor()->getMock();
        $this->object->setBlockInitializer($blockInitializer);
        $this->assertTrue($this->object->issetBlockInitializer());
    }

    /**
     * @covers NpPage\BlockPluginManager::validatePlugin
     */
    public function testValidatePlugin()
    {
        $block = $this->getMock('NpPage\\Block\\BlockInterface');
        $res = $this->object->validatePlugin($block);
        $this->assertNull($res);
    }

    /**
     * @covers NpPage\BlockPluginManager::validatePlugin
     * @expectedException NpPage\Exception\RuntimeException
     */
    public function testValidatePluginThrowsExceptionInvalidObject()
    {
        $this->object->validatePlugin(new \StdClass);
    }
}
