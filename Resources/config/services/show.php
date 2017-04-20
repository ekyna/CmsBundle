<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Show\Type\SeoType;
use Ekyna\Bundle\CmsBundle\Show\Type\TagsType;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Seo show type
        ->set('ekyna_cms.show_type.seo', SeoType::class)
            ->tag('ekyna_admin.show.type')

        // Tags show type
        ->set('ekyna_cms.show_type.tags', TagsType::class)
            ->tag('ekyna_admin.show.type')
    ;
};
