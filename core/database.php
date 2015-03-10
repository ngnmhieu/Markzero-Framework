<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$env  = App::$config->app->env;
$db = App::$config->database->{$env}; // database configurations
$is_dev = $env == 'development';
$path   = array(App::$MODEL_PATH, App::$CORE_PATH.'mvc');

$config = Setup::createAnnotationMetadataConfiguration($path, $is_dev);
$connection = array(
  'driver'   => 'pdo_mysql',
  'host'     => $db->host,
  'user'     => $db->user,
  'password' => $db->pass,
  'dbname'   => $db->dbname
);

App::$em = EntityManager::create($connection, $config);
