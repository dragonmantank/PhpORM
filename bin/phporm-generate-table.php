<?php

/**
 * CLI to generate an SQL Create Table Statement from an Entity
 * 
 * @author Chris Tankersley <chris@ctankersley.com>
 * @copyright 2010 Chris Tankersley
 * @package PhpORM_Cli
 */

require_once 'phporm-bootstrap.php';

$tool = new PhpORM_Cli_GenerateSql($argv[1], $argv[2]);
echo $tool->getSql();