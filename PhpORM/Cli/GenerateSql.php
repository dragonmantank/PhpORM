<?php

/**
 * Creates an SQL 'Create Table' based upon an entity
 *
 * @author Chris Tankersley <chris@ctankersley.com>
 * @copyright 2010 Chris Tankersley
 * @package PhpORM_Cli
 */
class PhpORM_Cli_GenerateSql
{
    /**
     * Use a MySQL database
     */
    const MYSQL = 'mysql';

    /**
     * Use a SQLite database
     */
    const SQLITE = 'sqlite';

    /**
     * Types that are allowed to have a length
     * @var array
     */
    protected $_hasLength = array('integer', 'varchar');

    /**
     * Regexes needed to pull out the different comments
     * @var array
     */
    protected $_regexes = array(
        'type' => '/ type\=([a-z_]*) /',
        'length' => '/ length\=([0-9]*) /',
        'default' => '/ default\=\"(.*)" /',
        'null' => '/ null /',
    );

    /**
     * Types that we support
     * @var array
     */
    protected $_validTypes = array(
        'boolean' => 'BOOL',
        'date' => 'DATE',
        'integer' => 'INT',
        'primary_autoincrement' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'text' => 'TEXT',
        'timestamp' => 'TIMESTAMP',
        'varchar' => 'VARCHAR',
    );

    /**
     * Name of the class we will interperet
     * @var string
     */
    protected $_className;

    /**
     * Name of the table we are generating
     * @var string
     */
    protected $_tableName;

    /**
     * The type of database we are generating
     * @var string
     */
    protected $_type;

    /**
     * Sets the name of the class we are working with
     * @param string $class
     * @param string $table_name
     * @param string $type
     */
    public function __construct($class, $table_name, $type = self::MYSQL)
    {
        $this->_className = $class;
        $this->_tableName = $table_name;
        $this->_type = $type;
    }

    /**
     * Builds an SQL Line for a property
     * @param ReflectionProperty $property
     * @return string
     */
    protected function _getDefinition($property)
    {
        $type = '';
        $length = '';
        $null = '';
        
        preg_match($this->_regexes['type'], $property->getDocComment(), $matches);
        if(count($matches) == 2) {
            if(array_key_exists($matches[1], $this->_validTypes)) {
                $type = $this->_validTypes[$matches[1]];

                if(in_array($matches[1], $this->_hasLength)) {
                    $length = $this->_getLength($property);
                }

                if($matches[1] != 'primary_autoincrement') {
                    $null = $this->_getNull($property);
                }

                $sql = '`'.$property->getName().'` '.$type.' '.$length.' '.$null;

                return $sql;
            } else {
                throw new Exception('Type "'.$matches[1].'" is not a supported SQL type');
            }
        } else {
            throw new Exception('Found '.count($matches).' when checking Type for property '.$property->getName());
        }
    }

    /**
     * Extracts the Length from a property
     * @param ReflectionProperty $property
     * @return string
     */
    protected function _getLength($property)
    {
        preg_match($this->_regexes['length'], $property->getDocComment(), $matches);

        if(count($matches) == 2) {
            return '('.$matches[1].')';
        } else {
            return '';
        }
    }

    /**
     * Determines if a Property is allowed to be null
     * @param ReflectionProperty $property
     * @return string
     */
    protected function _getNull($property)
    {
        preg_match($this->_regexes['null'], $property->getDocComment(), $matches);

        if(count($matches) == 1) {
            return 'NULL';
        } else {
            return 'NOT NULL';
        }
    }

    /**
     * Generates a block of SQL to create a table from an Entity
     * @return string
     */
    public function getSql()
    {
        $class = new ReflectionClass($this->_className);
        $definitions = array();
        foreach($class->getProperties() as $property) {
            if(strpos($property->getName(), '_') === false) {
                $definitions[] = $this->_getDefinition($property);
            }
        }
        $columns = implode(",\n", $definitions);
        
        $sql = "CREATE TABLE ".$this->_tableName." (".$columns.")";
        if($this->_type == self::MYSQL) {
            $sql .= " ENGINE=MYISAM";
        }

        return $sql.";";
    }
}