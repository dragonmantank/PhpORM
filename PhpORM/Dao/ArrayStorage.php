<?php

/**
 * Array Storage backend for data access
 *
 * ArrayStorage is a basic storage mechanism for temporary storage and quick
 * prototyping. Entities can be quickly stored and retrieved without being
 * written to sources outside of PHP. For examples of usage, see
 * /tests/phpORM/Dao/ArrayStorageTest.php
 *
 * @author Chris Tankersley <chris@ctankersley.com>
 * @package PhpORM_Dao
 */
class PhpORM_Dao_ArrayStorage extends PhpORM_Dao
{
    /**
     * Internal storage mechanism for this DAO
     * @var <type>
     */
    protected $_store = array();

    /**
     * Removes an object from the store
     * @param PhpORM_Entity $entity
     * @return bool
     */
    public function delete(PhpORM_Entity $entity)
    {
        foreach($this->_store as $key => $row) {
            if($row == $entity->toArray()) {
                unset($this->_store[$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the data store as-is
     * 
     * @return array
     */
    public function fetchAll($where = null)
    {
        return $this->_store;
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
        $foundRows = array();
        if(is_array($key)) {
            foreach($this->_store as $row) {
                $goodRow = false;
                foreach($key as $searchKey => $searchValue) {
                    if($row[$searchKey] == $searchValue) {
                        $goodRow = true;
                    } else {
                        $goodRow = false;
                        break;
                    }
                }

                if($goodRow) {
                    $foundRows[] = $row;
                }
            }
        } else {
            foreach($this->_store as $row) {
                if($row[$key] == $value) {
                    $foundRows[] = $row;
                }
            }
        }

        return $foundRows;
    }

    /**
     * Returns a row based upon the ID key
     *
     * @param mixed $id
     * @return array or null
     */
    public function find($id)
    {
        $discoveredRow = null;
        foreach($this->_store as $key => $row) {
            if($row['id'] == $id) {
                $discoveredRow = $row;
                break;
            }
        }

        return $discoveredRow;
    }

    /**
     * Inserts an entity into the store
     *
     * @param PhpORM_Entity $entity
     * @return bool
     */
    public function insert(PhpORM_Entity $entity)
    {
        $this->_store[] = $entity;
        return true;
    }

    /**
     * Updates a row in the store
     * @param PhpORM_Entity $entity
     * @return bool
     */
    public function update(PhpORM_Entity $entity)
    {
        $new = $entity->toArray();
        $original = $this->find($entity->id);
        $originalKey = null;
        foreach($this->_store as $key => $row) {
            if($row == $original) {
                $originalKey = $key;
                break;
            }
        }

        if($originalKey === null) {
            return false;
        } else {
            $this->_store[$originalKey] = array_merge($this->_store[$originalKey], $new);
            return true;
            
        }
    }

    /**
     * Resets the internal data store
     * @param array $store 
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }
}
