<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/config.php';

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('', __DIR__.'/../src');

$dbh = new \PDO(TESTS_DB_DSN, TESTS_DB_USERNAME, TESTS_DB_PASSWORD);
$dbh->exec("CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");