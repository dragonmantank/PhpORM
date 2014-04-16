<?php

namespace PhpORMTests\Storage;

use Aura\Sql\ExtendedPdo;
use PhpORM\Storage\AuraExtendedPdo;
use Aura\Sql_Query\QueryFactory;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class AuraExtendedPdoTest extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @return \PDO
     */
    protected function getPdo()
    {
        return new \PDO(TESTS_DB_DSN, TESTS_DB_USERNAME, TESTS_DB_PASSWORD);
    }

    /**
     * @return AuraExtendedPdo
     */
    protected function getStorage()
    {
        $pdo = $this->getPdo();
        $extendedPdo = new ExtendedPdo($pdo);
        return new AuraExtendedPdo($extendedPdo, new QueryFactory('mysql'));
    }

    protected function getConnection()
    {
        $pdo = $this->getPdo();
        return $this->createDefaultDBConnection($pdo, 'phporm-test');
    }

    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(TESTS_BASEDIR.'/datasets/users.xml');
    }

    /**
     * Makes sure that records are properly deleted
     *
     * @since 2014-04-15
     */
    public function testDelete()
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare('SELECT * FROM users');
        $stmt->execute();
        $this->assertEquals(3, $stmt->rowCount());

        $storage = $this->getStorage();
        $storage->delete(array('id' => 1), 'users');

        $stmt = $pdo->prepare('SELECT * FROM users');
        $stmt->execute();
        $this->assertEquals(2, $stmt->rowCount());

        $row = $pdo->query('SELECT * FROM users WHERE id=1')->fetch(\PDO::FETCH_ASSOC);
        $this->assertFalse($row);
    }

    /**
     * Tests as basic search where all results are returned from a table
     *
     * @since 2014-04-15
     */
    public function testFetchAll()
    {
        $originalData = array(
            1 => array('id' => 1, 'username' => 'root', 'password' => 'password'),
            2 => array('id' => 2, 'username' => 'user1', 'password' => 'password2'),
            3 => array('id' => 3, 'username' => 'user2', 'password' => 'password3'),
        );
        $storage = $this->getStorage();
        $result = $storage->fetchAll('users');

        $this->assertEquals(3, count($result));
        foreach($result as $row) {
            $this->assertEquals($originalData[$row['id']]['id'], $row['id']);
            $this->assertEquals($originalData[$row['id']]['username'], $row['username']);
            $this->assertEquals($originalData[$row['id']]['password'], $row['password']);
        }
    }

    /**
     * Tests as basic search where all results are returned from a table based on a criteria
     *
     * @since 2014-04-15
     */
    public function testFetchAllBy()
    {
        $data = array('id' => 1, 'username' => 'root', 'password' => 'password');
        $storage = $this->getStorage();
        $result = $storage->fetchAllBy(array('username' => 'root'), 'users');

        $this->assertEquals(1, count($result));
        $this->assertEquals($data['id'], $result[0]['id']);
        $this->assertEquals($data['username'], $result[0]['username']);
        $this->assertEquals($data['password'], $result[0]['password']);
    }

    /**
     * Tests as basic search where a single result is returned based on specific criteria
     *
     * @since 2014-04-15
     */
    public function testFind()
    {
        $data = array('id' => 1, 'username' => 'root', 'password' => 'password');
        $storage = $this->getStorage();
        $result = $storage->find(array('id' => 1), 'users');

        $this->assertEquals($data['id'], $result['id']);
        $this->assertEquals($data['username'], $result['username']);
        $this->assertEquals($data['password'], $result['password']);
    }

    /**
     * Makes sure that a standard Insert command is run when needed
     *
     * @since 2014-04-15
     */
    public function testInsert()
    {
        $storage = $this->getStorage();
        $newData = array('username' => 'newroot', 'password' => 'password');
        $storage->save($newData, 'users');

        $pdo = $this->getPdo();
        $stmt = $pdo->prepare('SELECT * FROM users');
        $stmt->execute();
        $this->assertEquals(4, $stmt->rowCount());

        $result = $pdo->query('SELECT * FROM users WHERE username="newroot"')->fetch(\PDO::FETCH_ASSOC);

        $this->assertTrue($result['id'] > 3);
        $this->assertEquals($newData['username'], $result['username']);
        $this->assertEquals($newData['password'], $result['password']);
    }

    /**
     * Makes sure that a standard Update command is run when needed
     *
     * @since 2014-04-15
     */
    public function testUpdate()
    {
        $storage = $this->getStorage();
        $newData = array('id' => 1, 'username' => 'newroot', 'password' => 'password');
        $storage->save($newData, 'users');

        $pdo = $this->getPdo();
        $stmt = $pdo->prepare('SELECT * FROM users');
        $stmt->execute();
        $this->assertEquals(3, $stmt->rowCount());

        $result = $pdo->query('SELECT * FROM users WHERE id=1')->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals($newData['id'], $result['id']);
        $this->assertEquals($newData['username'], $result['username']);
        $this->assertEquals($newData['password'], $result['password']);
    }
}