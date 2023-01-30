<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $configurator) {
	
	$services = $configurator->services()
		->defaults()
		->autowire()
		->autoconfigure()
	;
	
	$namespace = 'BaksDev\Users\Groups\Role';
	
	$services->load($namespace.'\Repository\\', __DIR__.'/../../Repository');
	
	$services->load($namespace.'\UseCase\\', __DIR__.'/../../UseCase')
		->exclude(__DIR__.'/../../UseCase/**/*DTO.php')
	;
	
};

