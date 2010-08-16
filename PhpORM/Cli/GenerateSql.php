<?php

class PhpORM_Cli_GenerateSql
{
    protected $_typeRegex = '/ type\=([a-z_]*) /';
    protected $_className;
    protected $_tableName;

    /**
     * Sets the name of the class we are working with
     * @param string $class
     * @param string $table_name
     */
    public function __construct($class, $table_name)
    {
        $this->_className = $class;
        $this->_tableName = $table_name;
    }

    /**
     * Returns the appropriate line for the type of column a property is
     * @param string $type
     * @param mixed $property
     * @return string
     */
    protected function _getColumnSql($type, $name)
    {
        switch($type) {
            case 'primary_autoincrement':
                return "`$name` INT NOT NULL AUTO_INCREMENT PRIMARY KEY";
                break;
            case 'integer':
                return "`$name` INT NOT NULL";
                break;
            case 'string':
                return "`$name` TEXT NOT NULL";
                break;
            case 'date':
                return "`$name` DATE NOT NULL";
                break;
        }
    }

    /**
     * Extracts the type of column a property is set to
     * @param string $string
     * @return string
     */
    protected function _getColumnType($string) {
        preg_match($this->_typeRegex, $string, $matches);

        return $matches;
    }

    /**
     * Generates a block of SQL to create a table from an Entity
     * @return string
     */
    public function getSql()
    {
        $class = new ReflectionClass($this->_className);
        $columns = array();
        foreach($class->getProperties() as $property) {
            if(strpos($property->getName(), '_') === false) {
                $type = $this->_getColumnType($property->getDocComment());

                // Determine the SQL for the column
                $columns[] = $this->_getColumnSql($type[1], $property->getName());
            }
        }
        $columns = implode(",\n", $columns);
        return "CREATE TABLE ".$this->_tableName." (".$columns.") ENGINE=MYISAM;\n";
    }
}