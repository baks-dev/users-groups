<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $config)
{
    $config->translator()->paths(['%kernel.project_dir%/src/Module/Users/Groups/Users/Resources/translations/']);
};



