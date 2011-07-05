<?php

class PDOTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Base PDO connection to use
	 * 
	 * @var PDO
	 */
	protected $pdo;
	
	/**
	 * Creates the tables that are needed for the tests and sets the PDO object
	 */
	protected function setUp()
	{
		$this->pdo = new PDO("sqlite:./database.db");
		$this->pdo->exec('CREATE TABLE `sample` (`id` INT, `value` VARCHAR(255));');
		$this->pdo->exec('INSERT INTO sample (`id`, `value`) VALUES (1, "Me!");');
		$this->pdo->exec('INSERT INTO sample (`id`, `value`) VALUES (2, "Too!");');
		$this->pdo->exec('INSERT INTO sample (`id`, `value`) VALUES (3, "Three!");');
		
		PhpORM_Dao_PDO::setConnection($this->pdo);
	}
	
	/**
	 * Destroys the test DB
	 */
	protected function tearDown()
	{
		unlink('./database.db');
	}

    /**
     * Returns all the entries in the store
     */
    public function testFetchAll()
    {
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
		
		$this->assertEquals(3, count($dao->fetchAll()));
    }

    /**
     * Returns a single row from the store
     */
    public function testFetchOne()
    {
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
		$data = array('id' => '1', 'value' => 'Me!');
		
		$this->assertEquals($data, $dao->fetchOneBy('id', 1));
    }

    public function testFetchOneCall()
    {
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
		$data = array('id' => '1', 'value' => 'Me!');
		
		$this->assertEquals($data, $dao->fetchOneByid(1));
    }

    /**
     * Deletes an entry from the store
     */
    public function testDelete()
    {
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
		$entity = new PhpORM_Entity_Generic();
        $entity->fromArray(array('id' => 2, 'value' => 'Too!'));
		$dao->delete($entity);
		
		$this->assertEquals(2, count($dao->fetchAll()));
		$this->assertNull($dao->find(2));
    }

    public function testAttemptToDeleteUnknownObject()
    {
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
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
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
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
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
		$expected = array(array('id' => 3, 'value' => 'Three!'));
		
		$result = $dao->fetchAllByvalue('Three!');
		$this->assertEquals($expected, $result);
    }

    /**
     * Returns all the entries that match multiple keys and values
     */
    public function testFindAllByArray()
    {
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
		$expected = array(array('id' => 3, 'value' => 'Three!'));
		
		$result = $dao->fetchAllBy(array('id' => 3, 'value' => 'Three!'));
		$this->assertEquals($expected, $result);
    }

    /**
     * Inserts an entity into the store
     */
    public function testInsert()
    {
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
		$data = array('id' => 5, 'value' => 'Five');
		
		$entity = new PhpORM_Entity_Generic();
		$entity->fromArray($data);
		
		$dao->insert($entity);
		$resultset = $dao->fetchAll();
		
		$this->assertEquals(4, count($resultset));
		$this->assertEquals($data, $dao->find(5));
    }

    /**
     * Updates a row in the store
     */
    public function testUpdate()
    {
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
		
		$original = $dao->find(1);
		$entity = new PhpORM_Entity_Generic($original);
		$entity->value = 'New Value';
		
		$dao->update($entity);
		$result = $dao->find($entity->id);
		
        $this->assertEquals($original['id'], $result['id']);
        $this->assertEquals('New Value', $result['value']);
    }
	
	/**
	 * Makes sure that the DAO returns a PDO object
	 */
	public function testGetConnection()
	{
		$pdo = new PhpORM_Dao_PDO();
		
		$this->assertEquals('PDO', get_class($pdo->getConnection()));
	}
	
	/**
	 * Makes sure we throw an exception if a table name isn't set
	 * @expectedException Exception
	 */
	public function testGetTableNameException()
	{
		$pdo = new PhpORM_Dao_PDO();
		$pdo->fetchAll();
	}
	
	/**
	 * Makes sure that the correct table name is returned
	 */
	public function testGetTableName()
	{
		$pdo = new PhpORM_Dao_PDO();
		$pdo->setTableName('sample');
		
		$this->assertEquals('sample', $pdo->getTableName());
	}
}
