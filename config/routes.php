<?php
/*
 * Version 0 
 * all the mapping must be regular expression
 * Version 1 proposal: {name:enforced_regular_expression} or {name}
 */

App::$router->draw(function($r) {
  $r->root('transaction#index');
  $r->map('get', '/transaction/create', 'transaction#create');

  $r->map('get', '/user', 'user#index');
  $r->map('get', '/user/show/([0-9]+)', 'user#show');

  $r->map('get', '/photo', 'photo#index');
  $r->map('get', '/photo/tag/(.+)', 'photo#tag');
  $r->map('get', '/admin/post/', 'admin/post#index');
});

