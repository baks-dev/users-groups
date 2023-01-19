<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $configurator)
{
    $services = $configurator->services()
      ->defaults()
      ->autowire()
      ->autoconfigure()
    ;
    
	$namespace = 'BaksDev\Users\Groups\Users';
	
    $services->load($namespace.'\Controller\\', __DIR__.'/../../Controller')
      ->tag('controller.service_arguments');

    $services->load($namespace.'\Repository\\', __DIR__.'/../../Repository');

    $services->load($namespace.'\UseCase\\', __DIR__.'/../../UseCase')
      ->exclude(__DIR__.'/../../UseCase/**/*DTO.php');
	
	
	
	//App\Service\XpCalculatorInterface: '@App\Service\XpCalculator'
	

//    $services->load('BaksDev\Users\Groups\Handler\\', '../../Handler')
//      //->exclude('../../Handler/**/*Command.php')
//      ->tag('controller.service_arguments');

};

