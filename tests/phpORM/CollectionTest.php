<?php

class CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $collection = new PhpORM_Collection();
        $this->assertEquals(0, count($collection));

        $collection->key = 'My value!';
        $this->assertEquals(1, count($collection));
    }

    public function testGet()
    {
        $collection = new PhpORM_Collection();
        $collection->Key1 = 'Value';
        $collection->Key2 = 'Value2';
        $collection->Key3 = 'Value3';

        $value = $collection->Key2;

        $this->assertEquals('Value2', $value);
    }

    public function testRemoveSingleItem()
    {
        $collection = new PhpORM_Collection();
        $collection->Key1 = 'Value';
        $collection->Key2 = 'Value2';
        $collection->Key3 = 'Value3';

        $this->assertEquals(3, count($collection));

        unset($collection['Key1']);
        $this->assertEquals(2, count($collection));
        $this->assertFalse(isset($collection->Key1));
    }

    public function testToArray()
    {
        $collection = new PhpORM_Collection();
        $collection->Key1 = 'Value';
        $collection->Key2 = 'Value2';
        $collection->Key3 = 'Value3';

        $this->assertTrue(is_array($collection->toArray()));
    }

    public function testForEach()
    {
        $collection = new PhpORM_Collection();
        $collection->Key1 = 'Value';
        $collection->Key2 = 'Value2';
        $collection->Key3 = 'Value3';

        $data = $collection->toArray();

        foreach($collection as $key => $value) {
            $this->assertEquals($data[$key], $value);
        }
    }

    public function testOffsetExists()
    {
        $collection = new PhpORM_Collection();
        $collection->MyKey = 'Exists';

        $this->assertTrue(isset($collection['MyKey']));
        $this->assertFalse(isset($collection['NotHEre']));
    }

    public function testOffsetGet()
    {
        $collection = new PhpORM_Collection();
        $collection->MyKey = 'Exists';

        $this->assertEquals('Exists', $collection['MyKey']);
    }

    public function testOffsetSet()
    {
        $collection = new PhpORM_Collection();
        $collection['MyKey'] = 'Exists';

        $this->assertTrue(isset($collection['MyKey']));
        $this->assertEquals('Exists', $collection['MyKey']);
    }

    public function testOffsetUnset()
    {
        $collection = new PhpORM_Collection();
        $collection['MyKey'] = 'Exists';

        $this->assertTrue(isset($collection['MyKey']));

        unset($collection['MyKey']);

        $this->assertFalse(isset($collection['MyKey']));
    }
}
