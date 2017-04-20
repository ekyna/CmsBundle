<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Factory\PageFactory;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Page factory
        ->set('ekyna_cms.factory.page', PageFactory::class)
            ->args([
                service('ekyna_cms.repository.page'),
            ])
    ;
};
