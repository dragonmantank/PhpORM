<?php

namespace PhpORMTests\Storage;

use Aura\Sql\ExtendedPdo;
use PhpORM\Storage\AuraExtendedPdo;
use Aura\Sql_Query\QueryFactory;

class PDOMock extends \PDO
{
    public function __construct() {}
}

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class AuraExtendedPdoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return AuraExtendedPdo
     */
    protected function getStorage()
    {
        $mockPDO = $this->getMockBuilder('\\PDOMock')
            ->setMethods(array('perform', 'fetchAll', 'fetchOne', 'lastInsertId'))
            ->getMock()
        ;
        return new AuraExtendedPdo($mockPDO, new QueryFactory('mysql'));
    }

    /**
     * Makes sure that records are properly deleted
     *
     * @since 2014-04-15
     */
    public function testDelete()
    {
        $mockPDO = $this->getMock('\\PDOMock', array('perform'));
        $mockPDO
            ->expects($this->once())
            ->method('perform')
            ->will($this->returnCallback(function($arg1, $arg2) {
                $sql = <<<ENDSQL
DELETE FROM `users`
WHERE
    id = :id
ENDSQL;
                if($arg1 == $sql && $arg2 = array('id' => 1)) {
                    return;
                } else {
                    throw new \Exception('We did not get the parameters we were expecting');
                }
            }))
        ;
        $storage = new AuraExtendedPdo($mockPDO, new QueryFactory('mysql'));
        $res = $storage->delete(array('id' => 1), 'users');

        $this->assertNull($res);
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
        $mockPDO = $this->getMock('\\PDOMock', array('fetchAll'));
        $mockPDO
            ->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnCallback(function($arg1) use ($originalData) {
                $sql = 'SELECT
    *
FROM
    `users`
ORDER BY
    id ASC';

                if($arg1 == $sql) {
                    return $originalData;
                } else {
                    array();
                }
            }))
        ;
        $storage = new AuraExtendedPdo($mockPDO, new QueryFactory('mysql'));

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
        $mockPDO = $this->getMock('\\PDOMock', array('fetchAll'));
        $mockPDO
            ->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnCallback(function($arg1, $arg2) use ($data) {
                $sql = 'SELECT
    *
FROM
    `users`
WHERE
    username = :username
ORDER BY
    id ASC';

                if($arg1 == $sql && $arg2 == array('username' => 'root')) {
                    return array($data);
                } else {
                    return array();
                }
            }))
        ;
        $storage = new AuraExtendedPdo($mockPDO, new QueryFactory('mysql'));
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
        $mockPDO = $this->getMock('\\PDOMock', array('fetchOne'));
        $mockPDO
            ->expects($this->once())
            ->method('fetchOne')
            ->will($this->returnCallback(function($arg1, $arg2) use ($data) {
                $sql = 'SELECT
    *
FROM
    `users`
WHERE
    id = :id
ORDER BY
    id ASC';

                if($arg1 == $sql && $arg2 == array('id' => 1)) {
                    return $data;
                } else {
                    return array();
                }
            }))
        ;
        $storage = new AuraExtendedPdo($mockPDO, new QueryFactory('mysql'));
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
        $newData = array('username' => 'newroot', 'password' => 'password');
        $mockPDO = $this->getMock('\\PDOMock', array('perform', 'lastInsertId'));
        $mockPDO
            ->expects($this->once())
            ->method('perform')
            ->will($this->returnCallback(function($arg1, $arg2) use ($newData) {
                $sql = 'INSERT INTO `users` (
    `username`,
    `password`
) VALUES (
    :username,
    :password
)';
                if($arg1 == $sql && $arg2 == $newData) {
                    return 1;
                } else {
                    return array();
                }
            }))
        ;
        $mockPDO
            ->expects($this->once())
            ->method('lastInsertId')
            ->will($this->returnValue(1));
        ;
        $storage = new AuraExtendedPdo($mockPDO, new QueryFactory('mysql'));

        $id = $storage->save($newData, 'users');

        $this->assertEquals(1, $id);
    }

    /**
     * Makes sure that a standard Update command is run when needed
     *
     * @since 2014-04-15
     */
    public function testUpdate()
    {
        $newData = array('id' => 1, 'username' => 'newroot', 'password' => 'password');
        $mockPDO = $this->getMock('\\PDOMock', array('perform'));
        $mockPDO
            ->expects($this->once())
            ->method('perform')
            ->will($this->returnCallback(function($arg1, $arg2) use ($newData) {
                $sql = 'UPDATE `users`
SET
    `id` = :id,
    `username` = :username,
    `password` = :password
WHERE
    id = :id';

                if($arg1 == $sql && $arg2 == $newData) {
                    return 1;
                } else {
                    return array();
                }
            }))
        ;

        $storage = new AuraExtendedPdo($mockPDO, new QueryFactory('mysql'));

        $id = $storage->save($newData, 'users');

        $this->assertEquals(1, $id);
    }
}