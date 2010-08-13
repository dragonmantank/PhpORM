<?php

/**
 * Facilitates access to Entities via a DAO
 *
 * Repositories are generally tied to a specific kind of entity and will return
 * objects and collections of objects of that specific type. This allows for
 * stronger typing that using a straight DAO, and also allows for DAOs to be
 * swapped out.
 *
 * @author Chris Tankersley <chris@ctankersley.com>
 * @package PhpORM_Repository
 */
abstract class PhpORM_Repository
{
    /**
     * Type of DAO that the repository uses
     * @var string
     */
    protected $_daoObjectName;

    /**
     * DAO instance
     * @var PhpORM_Dao
     */
    protected $_daoObject;

    /**
     * Type of entity this Repository is associated with and will return
     * @var string
     */
    protected $_entityObjectName;

    /**
     * Helps define some easy calls to other functions in the DAO
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function  __call($name, $arguments)
    {
        if(stripos($name, 'fetchAllBy') == 0) {
            $key = substr($name, 10);

            return $this->fetchAllBy($key, $arguments[0]);
        } else {
            throw new Exception('Unknown method '.$name.' passed');
        }
    }

    /**
     * Returns all of the entities from the data source that match the query
     *
     * @return mixed
     */
    public function fetchAll()
    {
        $dao = $this->getDaoObject();
        $result = $dao->fetchAll();

        $collection = array();
        foreach($result as $row) {
            $object = new $this->_entityObjectName;
            $object->fromArray($row);
            $collection[] = $object;
        }

        return $collection;
    }

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
    public function fetchAllBy($key, $value = null)
    {
        $dao = $this->getDaoObject();
        $result = $dao->fetchAllBy($key, $value);

        $collection = array();
        foreach($result as $row) {

            $object = new $this->_entityObjectName;
            $object->fromArray($row);
            $collection[] = $object;
        }

        return $collection;
    }

    /**
     * Returns a single entity by the primary key
     *
     * @param mixed $id
     * @return array
     */
    public function find($id)
    {
        $dao = $this->getDaoObject();
        $data = $dao->find($id);

        $object = new $this->_entityObjectName;
        if($data != null) {
            $object->fromArray($data[0]);
        }

        return $object;
    }

    /**
     * Returns the DAO that this object uses
     * @return PhpORM_Dao
     */
    public function getDao()
    {
        if($this->_daoObject == null) {
            $this->_daoObject = new $this->_daoObjectName;
        }

        return $this->_daoObject;
    }

    /**
     * Sets a specific DAO on the object
     *
     * This will override any existing DAO and cause the entity to use it
     * instead of the type specified in _daoObjectName
     *
     * @param PhpORM_Dao $dao
     */
    public function setDao(PhpORM_Dao_Interface $dao)
    {
        $this->_daoObject = $dao;
    }
}
