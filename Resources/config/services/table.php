<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Table\Column\TagsType;
use Ekyna\Bundle\CmsBundle\Table\Type\PageType;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Page table type
        ->set('ekyna_cms.table_type.page', PageType::class)
            ->args([
                service('router'),
            ])
            ->tag('table.type')

        // Tags table column type
        ->set('ekyna_cms.table_column_type.tags', TagsType::class)
            ->tag('table.column_type')
    ;
};
