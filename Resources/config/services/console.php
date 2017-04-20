<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Command\GenerateMenusCommand;
use Ekyna\Bundle\CmsBundle\Command\GeneratePagesCommand;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Generate menus command
        ->set('ekyna_cms.command.generate_menus', GenerateMenusCommand::class)
            ->args([
                service('ekyna_cms.generator.menu'),
            ])
            ->tag('console.command')

        // Generate pages command
        ->set('ekyna_cms.command.generate_pages', GeneratePagesCommand::class)
            ->args([
                service('ekyna_cms.generator.page'),
            ])
            ->tag('console.command')
    ;
};
