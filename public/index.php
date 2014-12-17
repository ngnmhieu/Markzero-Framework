<?php
include("../app/App.php");

# start the application
App::bootstrap();
App::$router->dispatch();
