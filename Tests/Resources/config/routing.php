<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('homepage', new Route('/', ['_controller' => 'TestBundle:Homepage:index']));

return $collection;
