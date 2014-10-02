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
