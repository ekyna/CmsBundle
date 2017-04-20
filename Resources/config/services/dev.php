<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\DataFixtures\ORM\CmsProcessor;
use Ekyna\Bundle\CmsBundle\DataFixtures\ORM\CmsProvider;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Cms fixtures provider
        ->set('ekyna_cms.fixtures.provider', CmsProvider::class)
            ->args([
                service('ekyna_cms.repository.tag'),
            ])
            ->tag('nelmio_alice.faker.provider')

        // Cms fixtures processor
        ->set('ekyna_cms.fixtures.processor', CmsProcessor::class)
            ->args([
                service('ekyna_cms.factory.seo'),
            ])
            ->tag('fidry_alice_data_fixtures.processor')
    ;
};
