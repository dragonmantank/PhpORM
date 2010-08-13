<?php

/**
 * A Representation of some sort of thing in a program
 *
 * @author Chris Tankersley <chris@ctankersley.com>
 * @package PhpORM_Entity
 */
abstract class PhpORM_Entity implements ArrayAccess
{
    /**
     * Main key of the object
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Determines how strict the entity is in regards to attributes
     *
     * Setting this true allows the entity to accept any attribute
     * Setting this false causes the entity to validate all attribute sets/gets
     * @var boolean
     */
    protected $_allowDynamicAttributes = true;

    /**
     * DAO Name that this entity uses for storage
     * @var string
     */
    protected $_daoObjectName;

    /**
     * DAO instance to use for storage
     * @var PhpORM_Dao
     */
    protected $_dao;

    /**
     * Relationship descriptions for the entity
     *
     * Format is:
     *    RelationName = array(
     *       repo = RepoName
     *       entity = EntityName
     *       key = array(foreign = column, local = column)
     *       type = (one|many)
     *    )
     *
     * @var array
     */
    protected $_relationships = array();

    /**
     * Relationships that are currently loaded
     *
     * @var array
     */
    protected $_relations = array();

    /**
     * Allows an entity to allow/disallow dynamic attributes
     *
     * @param boolean $value
     */
    public function allowDynamicAttributes($value = true)
    {
        $this->_allowDynamicAttributes = $value;
    }

    public function __construct(array $data = array())
    {
        if(count($data)) {
            $this->fromArray($data);
        }
    }

    /**
     * Returns an attribute from the entity
     *
     * If _ignoreMissingProperty is set to 'false' it will throw an exception
     * if the attribute doesn't exist in the entity.
     *
     * @param string $property
     * @throws Exception
     * @return mixed
     * @throws Exception
     */
    public function __get($property)
    {
        if (array_key_exists($property, get_object_vars($this)) || $this->_allowDynamicAttributes) {
            return $this->$property;
        } else {
            if(array_key_exists($property, $this->_relationships)) {
                return $this->getRelationship($property);
            } else {
                throw new Exception('Requested property ' . $property . ' does not exist, could not retrieve');
            }
        }
    }

    /**
     * Returns the DAO that this object uses
     * @return PhpORM_Dao
     */
    public function getDao()
    {
        if ($this->_dao == null) {
            $this->_dao = new $this->_daoObjectName();
        }

        return $this->_dao;
    }

    /**
     * Returns a relationship
     *
     * If the relationship is not loaded, it will load it first and then return
     * it.
     *
     * @param string $name
     * @return mixed
     */
    public function getRelationship($name)
    {
        if(!array_key_exists($name, $this->_relations)) {
            $this->loadRelationship($name);
        }

        return $this->_relations[$name];
    }

    /**
     * Sets all of the entity attributes from an array
     * @param array $data 
     */
    public function fromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Loads a relationship into the entity
     *
     * @param string $name
     */
    public function loadRelationship($name)
    {
        $relation = $this->_relationships[$name];
        $repo = $relation['repo'];

        if($relation['type'] == 'one') {
            $this->_relations[$property] = $repo->fetchOneBy($relation['key']['foreign'], $relation['key']['local']);
        } elseif($relation['type'] == 'many') {
            $this->_relations[$property] = $repo->fetchAllBy($relation['key']['foreign'], $relation['key']['local']);
        }
    }

    /**
     * Checks to see if a key exists in the store
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    /**
     * Returns the specified element from the store via the array format
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Sets an element to a specified value via the array format
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Removes an element from the store via the array format
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->$offset = null;
    }

    /**
     * Saves the entity to its data source
     *
     * This will return the primary key value of the entity
     * 
     * @return mixed
     */
    public function save() 
    {
        $dao = $this->getDao();

        if ($this->$this->_primary == null) {
            $this->$this->_primary = $dao->insert($this);
        } else {
            $dao->update($this);
        }
    }

    /**
     * Sets an attribute from the entity
     *
     * If _ignoreMissingProperty is set to 'false' it will throw an exception
     * if the attribute doesn't exist in the entity.
     *
     * @param string $property
     * @param mixed $value
     * @throws Exception
     * @return mixed
     */
    public function __set($property, $value)
    {
        if (array_key_exists($property, get_object_vars($this)) || $this->_allowDynamicAttributes) {
            $this->$property = $value;
        } else {
            throw new Exception('Requested property ' . $property . ' does not exist, could not set');
        }
    }

    /**
     * Sets a specific DAO on the object
     *
     * This will override any existing DAO and cause the entity to use it
     * instead of the type specified in _daoObjectName
     * 
     * @param PhpORM_Dao $dao
     */
    public function setDao(PhpORM_Dao $dao)
    {
        $this->_dao = $dao;
    }

    /**
     * Returns an instance of the Entity as an array
     *
     * This will strip out any attributes with starting with '_'
     *
     * @return array
     */
    public function toArray() {
        $data = array();
        foreach (get_object_vars($this) as $key => $value) {
            if(strpos($key, '_') === false) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

}
