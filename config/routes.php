<?php
/*
 * Version 0 
 * all the mapping must be regular expression
 * Version 1 proposal: {name:enforced_regular_expression} or {name}
 */

App::$router->draw(function($r) {
  $r->root('transaction#index');

  $r->map('get', '/transaction/', 'transaction#index');
  $r->map('get', '/transaction/([0-9]+)', 'transaction#show');
  $r->map('get', '/transaction/index', 'transaction#index');
  $r->map('get', '/transaction/add', 'transaction#add');
  $r->map('get', '/transaction/([0-9]+)/edit', 'transaction#edit');
  $r->map('get', '/transaction/([0-9]+)/delete', 'transaction#delete');
  $r->map('post', '/transaction/', 'transaction#create');
  $r->map('post', '/transaction/([0-9]+)', 'transaction#update');

  $r->map('get', '/category/', 'category#index');
  $r->map('get', '/category/index', 'category#index');
  $r->map('get', '/category/add', 'category#add');
  $r->map('get', '/category/([0-9]+)/edit', 'category#edit');
  $r->map('get', '/category/([0-9]+)/delete', 'category#delete');
  $r->map('post', '/category/', 'category#create');
  $r->map('post', '/category/([0-9]+)/update', 'category#update');

  $r->map('get', '/user', 'user#index');
  $r->map('get', '/user/show/([0-9]+)', 'user#show');
});

