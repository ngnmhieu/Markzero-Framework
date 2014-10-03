<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once('./app/app.php');

App::bootstrap();

return ConsoleRunner::createHelperSet(App::$entity_manager);
