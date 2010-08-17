<?php

/**
 * Collection mechanism for storing objects and data
 *
 * This is a generic ArrayObject that allows a unified interface for working
 * with collections of objects.
 *
 * @author Chris Tankersley <chris@ctankersley.com>
 * @copyright 2010 Chris Tankersley
 * @package PhpORM_Collection
 */
class PhpORM_Collection extends ArrayObject
{

    /**
     * Helps define some easy calls to other functions in the DAO
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function  __call($name, $arguments)
    {
        if(stripos($name, 'fetchAllBy') === 0) {
            $key = substr($name, 10);

            return $this->fetchAllBy($key, $arguments[0]);
        }

        if(stripos($name, 'fetchOneBy') === 0) {
            $key = substr($name, 10);

            return $this->fetchOneBy($key, $arguments[0]);
        }

        throw new Exception('Unknown method '.$name.' passed');
    }

    /**
     * Searches the collection and returns all the objects that match the search
     *
     * @param mixed $key
     * @param mixed $value
     * @return PhpORM_Collection
     */
    public function fetchAllBy($key, $value)
    {
        $store = new PhpORM_Dao_ArrayStorage();
        $store->setStore($this->toArray());

        return new PhpORM_Collection($store->fetchAllBy($key, $value));
    }

    /**
     * Returns a single result from the Collection
     * 
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     */
    public function fetchOneBy($key, $value)
    {
        $store = new PhpORM_Dao_ArrayStorage();
        $store->setStore($this->toArray());

        return $store->fetchOneBy($key, $value);
    }

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
