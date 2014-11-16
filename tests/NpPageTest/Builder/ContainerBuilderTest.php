<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPageTest\Builder;

use Flower\Test\TestTool;
use NpPage\Block\Block;
use NpPage\Block\Container;
use NpPage\Builder\ContainerBuilder;
use Zend\ServiceManager\ServiceManager;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-16 at 16:12:43.
 */
class ContainerBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ContainerBuilder;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers NpPage\Builder\ContainerBuilder::build
     */
    public function testBuild()
    {
        $block = new Container;
        $block->setOption('builder', array('foo'));
        $this->object->build($block);
        $this->assertEquals(array('foo'), TestTool::getPropertyValue($this->object, 'buildOptions'));

        //callbackで、builderを取得してblockを操作できる
        $block = new Container;
        $block->setOption('builder', function ($builder) {
            $b = $builder->thisBlock();
            $b->setProperty('debug', $builder);
            return $b;
        });
        $this->object->build($block);
        $this->assertSame($this->object, $block->getProperty('debug'));
        $this->assertSame($block, TestTool::getPropertyValue($this->object, 'block'));
    }

    /**
     * @covers NpPage\Builder\ContainerBuilder::setRepository
     */
    public function testSetRepository()
    {
        $repository = $this->getMock('NpPage\\RepositoryInterface');
        $this->object->setRepository($repository);
        $this->assertSame($repository, TestTool::getPropertyValue($this->object, 'repository'));
    }

    /**
     * @covers NpPage\Builder\ContainerBuilder::setRepository
     */
    public function testSetRepositoryNull()
    {
        $sl = new ServiceManager;
        $repository = $this->getMock('NpPage\\RepositoryInterface');
        $sl->setService(TestTool::getPropertyValue($this->object, 'repositoryServiceName'), $repository);
        $this->object->setServiceLocator($sl);
        $this->object->setRepository();
        $this->assertSame($repository, TestTool::getPropertyValue($this->object, 'repository'));
    }

    /**
     * @covers NpPage\Builder\ContainerBuilder::getRepository
     */
    public function testGetRepository()
    {
        $repository = $this->getMock('NpPage\\RepositoryInterface');
        $this->object->setRepository($repository);
        $this->assertSame($repository, $this->object->getRepository());
    }

    /**
     * @dataProvider providesBlockConfigArray
     * @covers NpPage\Builder\ContainerBuilder::addChildrenWithDefinition
     */
    public function testAddChildrenWithDefinition($def, array $expects)
    {
        $parent = new Container;
        $child = new Block;
        $repository = $this->getMock('NpPage\\RepositoryInterface');
        $repository->expects($this->once())
                ->method('getBlock')
                // $name $config $load
                ->with($this->equalTo($expects[0]), $this->equalTo($expects[1]), $this->equalTo($expects[2]))
                ->will($this->returnValue($child));
        $this->object->setRepository($repository);
        $this->object->addChildrenWithDefinition($parent, $def);
    }

    public function providesBlockConfigArray()
    {
        return array(
            array(
                array('foo'),
                array('foo', array(), true),
            ),
            array(
                array('foo' => array()),
                array('foo', array('name' => 'foo'), false),
            ),
            array(
                array('foo' => array(
                    'name' => 'bar',
                )),
                array('bar', array('name' => 'bar'), false),
            ),
            array(
                array('foo' => array(
                    'name' => 'bar',
                    'load' => true,
                )),
                array('bar', array('name' => 'bar', 'load' => true), true),
            ),
            array(
                array(array(
                    'name' => 'foo',
                    'load' => true,
                    'a' => 'b',
                )),
                array('foo', array('name' => 'foo', 'a' => 'b', 'load' => true), true),
            ),
            array(
                array(array(
                    'name' => 'foo',
                    'load' => true,
                    'a' => 'b',
                    'config' => array(
                        'a' => 'A',
                    ),
                )),
                array('foo', array('name' => 'foo', 'a' => 'A'), true),
            ),
        );
    }

    /**
     * @depends testAddChildrenWithDefinition
     * @dataProvider providesBlockConfigArray
     *
     */
    public function testBuildWithBlockArray($def, array $expects)
    {
        $parent = new Container;
        $child = new Block;
        $repository = $this->getMock('NpPage\\RepositoryInterface');
        $repository->expects($this->once())
                ->method('getBlock')
                // $name $config $load
                ->with($this->equalTo($expects[0]), $this->equalTo($expects[1]), $this->equalTo($expects[2]))
                ->will($this->returnValue($child));
        $this->object->setRepository($repository);
        $parent->setBlockArrayConfig($def);
        $this->object->build($parent);
    }

    /**
     * @depends testAddChildrenWithDefinition
     * @dataProvider providesBlockConfigArray
     *
     */
    public function testBuildWithBuildOption($def, array $expects)
    {
        $parent = new Container;
        $child = new Block;
        $repository = $this->getMock('NpPage\\RepositoryInterface');
        $repository->expects($this->once())
                ->method('getBlock')
                // $name $config $load
                ->with($this->equalTo($expects[0]), $this->equalTo($expects[1]), $this->equalTo($expects[2]))
                ->will($this->returnValue($child));
        $this->object->setRepository($repository);
        $parent->setOption('builder', array('blocks' => $def));
        $this->object->build($parent);
    }

    /**
     * @covers NpPage\Builder\ContainerBuilder::load
     */
    public function testLoad()
    {
        $name = 'foo';
        $result = array('a' => 'A');
        $repository = $this->getMock('NpPage\\RepositoryInterface');
        $this->object->setRepository($repository);
        $repository->expects($this->once())
                ->method('loadBlockConfig')
                // $name $config $load
                ->with($this->equalTo($name))
                ->will($this->returnValue($result));
        $this->object->load($name);
    }

    /**
     * @covers NpPage\Builder\ContainerBuilder::get
     */
    public function testGet()
    {
        $name = 'foo';
        $config = array('a' => 'A');
        $repository = $this->getMock('NpPage\\RepositoryInterface');
        $this->object->setRepository($repository);
        $repository->expects($this->once())
                ->method('getBlock')
                // $name $config $load
                ->with($this->equalTo($name), $this->equalTo($config));
        $this->object->get($name, $config);
    }

    /**
     * @dataProvider providesInsertDefs
     * @covers NpPage\Builder\ContainerBuilder::insert
     */
    public function testInsert()
    {
        $container = new Container;
        $block = new Block;
        $args = func_get_args();
        $expects = array_shift($args);
        $repository = $this->getMock('NpPage\\RepositoryInterface');
        $this->object->setRepository($repository);
        $repository->expects($this->once())
                ->method('getBlock')
                // $name $config $load
                ->with($this->equalTo($expects[0]), $this->equalTo($expects[1]), $this->equalTo($expects[2]))
                ->will($this->returnValue($block));
        $this->object->build($container);
        call_user_func_array(array($this->object, 'insert'), $args);
    }

    public function providesInsertDefs()
    {
        return array(
            array(
                array('foo', array(), true),//expects first
                'foo',
            ),
            array(
                array('foo/bar', array('class' => 'simple'), false),//expects
                //順不同
                false,
                array('class' => 'simple'),
                'foo/bar',
            ),
            array(
                array('foo/bar', array('class' => 'simple', 'name' => 'baz'), false),//expects
                //順不同
                false,
                array('name' => 'baz', 'class' => 'simple'),
                'foo/bar',
            ),
        );
    }
}
