<?php
include("../app/app.php");

# start the application
App::bootstrap();
App::$router->dispatch();
