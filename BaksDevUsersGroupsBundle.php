<?php
/*
 * This file is part of the FreshCentrifugoBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace BaksDev\Users\Groups;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class BaksDevUsersGroupsBundle extends AbstractBundle
{
	
	public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder) : void
	{
		$path = __DIR__.'/Group/Resources/config';
		
		foreach(new \DirectoryIterator($path) as $config)
		{
			if($config->isDot() || $config->isDir())
			{
				continue;
			}
			
			if($config->isFile() && $config->getFilename() !== 'routes.php')
			{
				$container->import($config->getPathname());
			}
		}
		
		$path = __DIR__.'/Role/Resources/config';
		
		foreach(new \DirectoryIterator($path) as $config)
		{
			if($config->isDot() || $config->isDir())
			{
				continue;
			}
			
			if($config->isFile() && $config->getFilename() !== 'routes.php')
			{
				$container->import($config->getPathname());
			}
		}
		
		$path = __DIR__.'/Users/Resources/config';
		
		foreach(new \DirectoryIterator($path) as $config)
		{
			if($config->isDot() || $config->isDir())
			{
				continue;
			}
			
			if($config->isFile() && $config->getFilename() !== 'routes.php')
			{
				$container->import($config->getPathname());
			}
		}
		
	}
}
