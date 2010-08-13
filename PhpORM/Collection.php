<?php

/**
 * Collection mechanism for storing objects and data
 *
 * This is a generic ArrayObject that allows a unified interface for working
 * with collections of objects.
 *
 * @author Chris Tankersley <chris@ctankersley.com>
 * @package PhpORM_Collection
 */
class PhpORM_Collection extends ArrayObject
{
    /**
     * Resets the internal storage to the new array
     * @param array $store
     */
    public function fromArray(array $store)
    {
        $this->exchangeArray($store);
    }

    /**
     * Allows for object notation to get things from storage
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this[$name];
    }

    /**
     * Allows for object notation to set things into storage
     * @param string $name
     * @param mixed $value
     */
    public function  __set($name, $value)
    {
        $this[$name] = $value;
    }

    /**
     * Returns a copy of the collection in array format
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }
}
