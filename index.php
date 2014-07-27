<?php

define('DOCROOT', __DIR__ . DIRECTORY_SEPARATOR);

define('APPPATH', __DIR__ . DIRECTORY_SEPARATOR . 'geoapp' . DIRECTORY_SEPARATOR);

require APPPATH . 'bootstrap.php';

//create the controller and execute the action
$loader = new Loader($_GET);
$controller = $loader->createController();
$controller->execute();
