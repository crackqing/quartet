<?php 

$router->get('phone/dashboard','PhoneController@index');
$router->get('phone/dashboard/charts','PhoneController@indexCharts');

$router->get('phone/player','PhoneController@player');
$router->get('phone/player/charts','PhoneController@playerCharts');

$router->get('phone/agent','PhoneController@agent');
$router->get('phone/agent/charts','PhoneController@agentCharts');