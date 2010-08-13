<?php

/**
 * Abstract Data Access Object class for working with different data sources
 *
 * This sets up the basic functions that are needed for a DAO as well as sets
 * up some magic methods that make it easier to call some of the built-in
 * functions.
 *
 * @author Chris Tankersley <chris@ctankersley.com>
 * @package PhpORM_Dao
 */
abstract class PhpORM_Dao
{
    /**
     * Helps define some easy calls to other functions in the DAO
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (stripos($name, 'fetchAllBy') === 0) {
            $key = substr($name, 10);
            return $this->fetchAllBy($key, $arguments[0]);
        }

        if (stripos($name, 'fetchOneBy') === 0) {
            $key = substr($name, 10);
            return $this->fetchOneBy($key, $arguments[0]);
        }

       throw new Exception('Unknown method ' . $name . ' passed');
    }

    /**
     * Removes an entity from the data source
     *
     * @param PhpORM_Entity $entity
     * @return boolean
     */
    abstract public function delete(PhpORM_Entity $entity);

    /**
     * Returns all of the entities from the data source that match the query
     *
     * @param mixed $where
     * @return array
     */
    abstract public function fetchAll($where = null);

    /**
     * Performs a search based upon a specific column
     *
     * If $key is an array, value is ignored and $key should be in
     * 'columnname' => 'value'.
     *
     * @param mixed $key
     * @param mixed $value
     * @return array
     */
    abstract public function fetchAllBy($key, $value = null);

    /**
     * Performs a search based upon a specific column and returns a single result
     *
     * If $key is an array, value is ignored and $key should be in
     * 'columnname' => 'value'.
     *
     * @param mixed $key
     * @param mixed $value
     * @return array
     */
    abstract public function fetchOneBy($key, $value = null);

    /**
     * Returns a single entity by the primary key
     *
     * @param mixed $id
     * @return array
     */
    abstract public function find($id);

    /**
     * Inserts an entity into the data source
     *
     * This returns the primary key of the object once inserted
     *
     * @param PhpORM_Entity $entity
     * @return mixed
     */
    abstract public function insert(PhpORM_Entity $entity);

    /**
     * Updates an entity in the data source
     *
     * @param PhpORM_Entity $entity
     * @return boolean
     */
    abstract public function update(PhpORM_Entity $entity);
}
