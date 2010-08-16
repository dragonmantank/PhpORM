<?php

require_once 'phporm-bootstrap.php';

$tool = new PhpORM_Cli_GenerateSql($argv[1], $argv[2]);
echo $tool->getSql();