<?php
// TODO: this is the file testing activerecord
// should be reorganized somehow. Or it is ok?

/* Database informations */
$dev  = App::$config->database->development;
$prod = App::$config->database->production;
$connections = array(
    "development" => "mysql://{$dev->user}:{$dev->pass}@{$dev->host}/{$dev->dbname}",
    "production" => "mysql://{$prod->user}:{$prod->pass}@{$prod->host}/{$prod->dbname}"
);

ActiveRecord\Config::initialize(function ($cfg) use ($connections) {
  $cfg->set_model_directory(App::$MODEL_DIR);

  $cfg->set_connections($connections);
  $cfg->set_default_connection(App::$config->app->env);
});

// Doctrine

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$env  = App::$config->app->env;
$db = App::$config->database->{$env}; // database configurations
$is_dev = $env == 'development';
$path   = [App::$APP_PATH.'app/models/'];

$config = Setup::createAnnotationMetadataConfiguration($path, $is_dev);
$connection = [
  'driver' => 'pdo_mysql',
  'host'   => $db->host,
  'user'   => $db->user,
  'password'   => $db->pass,
  'dbname'   => $db->dbname
];

App::$entity_manager = EntityManager::create($connection, $config);
