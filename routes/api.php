<?php
$router = new \Core\Router();
$router->post('register', 'Api\AuthController@register');
$router->post('login', 'Api\AuthController@login');
$router->get('domains', 'Api\DomainController@index');
$router->post('domains', 'Api\DomainController@store');
$router->patch('domains', 'Api\DomainController@update');
$router->get('links', 'Api\LinkController@index');
$router->post('links', 'Api\LinkController@store');
$router->patch('links', 'Api\LinkController@update');
$router->delete('links', 'Api\LinkController@destroy');
$router->get('{link}', 'Api\LinkController@show');
$router->resolver();