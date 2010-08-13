<?php

class EntityTest extends PHPUnit_Framework_TestCase
{
    public function testIgnoreMissingProperty()
    {
        $entity = new PhpORM_Entity_Generic();
        $entity->sample = "Value";

        $this->assertEquals("Value", $entity->sample);
    }
}
