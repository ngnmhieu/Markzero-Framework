<?php
/*
 * Version 0 
 * all the mapping must be regular expression
 */

App::$router->draw(function($r) {
  $r->root('user#index');
  $r->map('get', '/user', 'user#index');
  $r->map('get', '/user/login', 'user#login');
  $r->map('get', '/user/show/([0-9]*)', 'user#show');
});

