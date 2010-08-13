<?php

class ArrayStorageTest extends PHPUnit_Framework_TestCase
{
    protected function _getDao()
    {
        $dao = new PhpORM_Dao_ArrayStorage();
        $data = array(
            array('id' => 1, 'value' => 'Me!'),
            array('id' => 2, 'value' => 'Too!'),
            array('id' => 3, 'value' => 'No!'),
        );
        $dao->setStore($data);

        return $dao;
    }

    /**
     * Returns all the entries in the store
     */
    public function testFetchAll()
    {
        $dao = new PhpORM_Dao_ArrayStorage();
        $data = array(
            array('id' => 1, 'value' => 'Me!'),
            array('id' => 2, 'value' => 'Too!'),
            array('id' => 3, 'value' => 'No!'),
        );
        $dao->setStore($data);

        $this->assertEquals($dao->fetchAll(), $data);
    }

    /**
     * Deletes an entry from the store
     */
    public function testDelete()
    {
        $dao = $this->_getDao();
        
        $entity = new PhpORM_Entity_Generic();
        $entity->fromArray(array('id' => 2, 'value' => 'Too!'));
        $dao->delete($entity);

        $this->assertEquals(2, count($dao->fetchAll()));
        $this->assertNull($dao->find(2));
    }

    public function testAttemptToDeleteUnknownObject()
    {
        $dao = $this->_getDao();

        $entity = new PhpORM_Entity_Generic();
        $entity->fromArray(array('id' => 20, 'value' => 'Too!'));
        
        $this->assertFalse($dao->delete($entity));
        $this->assertEquals(3, count($dao->fetchAll()));
    }

    /**
     * Returns a specific row based upon an ID search
     */
    public function testFind()
    {
        $dao = $this->_getDao();
        $row = $dao->find(1);

        $this->assertTrue(is_array($row));
        $this->assertEquals(1, $row['id']);
        $this->assertEquals('Me!', $row['value']);
    }

    /**
     * Returns all the entries that match a specific key value
     */
    public function testFindAllBy()
    {
        $dao = $this->_getDao();
        $expected = array(array('id' => 3, 'value' => 'No!'));

        $result = $dao->fetchAllByvalue('No!');

        $this->assertEquals($expected, $result);
    }

    /**
     * Returns all the entries that match multiple keys and values
     */
    public function testFindAllByArray()
    {
        $dao = $this->_getDao();

        $expected = array(array('id' => 1, 'value' => 'Me!'));
        $result = $dao->fetchAllBy(array('id' => 1, 'value' => 'Me!'));
        $this->assertEquals($expected, $result);

        $result = $dao->fetchAllBy(array('id' => 2, 'value' => 'Me!'));
        $this->assertEquals(array(), $result);
    }

    /**
     * Inserts an entity into the store
     */
    public function testInsert()
    {
        $dao = $this->_getDao();
        $data = array('id' => 5, 'value' => 'New Value');
        $entity = new PhpORM_Entity_Generic();
        $entity->fromArray($data);
        $dao->insert($entity);
        
        $this->assertEquals(4, count($dao->fetchAll()));
        $this->assertEquals($data, $dao->find(5)->toArray());
    }

    /**
     * Updates a row in the store
     */
    public function testUpdate()
    {
        $dao = $this->_getDao();
        $original = $dao->find(1);
        $entity = new PhpORM_Entity_Generic();
        $entity->fromArray($original);
        $entity->value = 'New Value';

        $dao->update($entity);
        $result = $dao->find($entity->id);
        $this->assertEquals($original['id'], $result['id']);
        $this->assertEquals('New Value', $result['value']);
    }

    /**
     * Resets a store to new values
     */
    public function testSetStore()
    {
        $data = array('id' => 1, 'value' => 'Never been here before');
        $dao = $this->_getDao();
        $dao->setStore($data);

        $this->assertEquals($data, $dao->fetchAll());
    }
}
