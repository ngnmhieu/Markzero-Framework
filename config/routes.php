<?php
/*
 * Version 0 
 * all the mapping must be regular expression
 * Version 1 proposal: {name:enforced_regular_expression} or {name}
 */

// TODO: add scope and domain, subfolder for controller
App::$router->draw(function($r) {
  $r->root('photo#index');
  $r->map('get', '/user', 'user#index');
  $r->map('get', '/user/login', 'user#login');
  $r->map('get', '/user/show/([0-9]+)', 'user#show');

  $r->map('get', '/photo', 'photo#index');
  $r->map('get', '/photo/tag/(.+)', 'photo#tag');
});

