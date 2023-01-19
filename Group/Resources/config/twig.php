<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\TwigConfig;

return static function (TwigConfig $config)
{
    $config->path('%kernel.project_dir%/src/Module/Users/Groups/Group/Resources/view', 'UserGroup');
};




