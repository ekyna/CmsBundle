<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Service\Serializer\AttributesNormalizer;
use Ekyna\Bundle\CmsBundle\Service\Serializer\TabsNormalizer;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // (Editor) Attributes normalizer
        ->set('ekyna_cms.normalizer.attributes', AttributesNormalizer::class)
            ->tag('serializer.normalizer')
            ->tag('serializer.denormalizer')

        // (Editor) Tabs normalizer
        ->set('ekyna_cms.normalizer.tabs', TabsNormalizer::class)
            ->args([
                service('ekyna_cms.editor.document_locale_provider'),
                service('ekyna_media.repository.media'),
            ])
            ->tag('serializer.normalizer')
            ->tag('serializer.denormalizer')
    ;
};
