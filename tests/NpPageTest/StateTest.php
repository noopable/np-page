<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPageTest;

use NpPage\State;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-12 at 17:55:04.
 */
class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var State
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new State;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers NpPage\State::checkFlag
     */
    public function testCheckFlag()
    {
        $this->assertFalse($this->object->checkFlag(State::CONFIGURED));
        $this->assertFalse($this->object->checkFlag(State::INITIALIZED));
        $this->assertFalse($this->object->checkFlag(State::PREPARE_VIEW_MODEL));
    }

    /**
     * @covers NpPage\State::setFlag
     */
    public function testSetFlag()
    {
        $this->object->setFlag(State::CONFIGURED);
        $this->assertTrue($this->object->checkFlag(State::CONFIGURED));

        $this->object->setFlag(State::CONFIGURED, false);
        $this->assertFalse($this->object->checkFlag(State::CONFIGURED));
        $this->assertFalse($this->object->checkFlag(State::INITIALIZED));
        $this->assertFalse($this->object->checkFlag(State::PREPARE_VIEW_MODEL));
    }





    /**
     * @covers NpPage\State::serialize
     */
    public function testSerialize()
    {
        $serialized = $this->object->serialize();
        $ref = new \ReflectionObject($this->object);
        $val = $ref->getProperty('values');
        $val->setAccessible(true);
        $this->assertEquals($val->getValue($this->object), unserialize($serialized));

        $serialized = serialize($this->object);
        $object = unserialize($serialized);
        $this->assertEquals($this->object, $object);
    }

    /**
     * @covers NpPage\State::unserialize
     */
    public function testUnserialize()
    {
        $values = array(
            'CONFIGURED' => true,
            'INITIALIZED' => false,
            'PREPARE_VIEW_MODEL' => false,
        );

        $this->object->unserialize(serialize($values));
        $this->assertTrue($this->object->checkFlag(State::CONFIGURED));

        $serialized = serialize($this->object);
        $object = unserialize($serialized);
        $this->assertTrue($object->checkFlag(State::CONFIGURED));
    }
}
