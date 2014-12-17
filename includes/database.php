<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$env  = App::$config->app->env;
$db = App::$config->database->{$env}; // database configurations
$is_dev = $env == 'development';
$path   = array(App::$APP_PATH.'app/models/', App::$APP_PATH.'app/base/');

$config = Setup::createAnnotationMetadataConfiguration($path, $is_dev);
$connection = array(
  'driver' => 'pdo_mysql',
  'host'   => $db->host,
  'user'   => $db->user,
  'password'   => $db->pass,
  'dbname'   => $db->dbname
);

App::$entity_manager = EntityManager::create($connection, $config);
