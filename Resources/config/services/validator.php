<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Validator\Constraints\BlockValidator;
use Ekyna\Bundle\CmsBundle\Validator\Constraints\ContainerValidator;
use Ekyna\Bundle\CmsBundle\Validator\Constraints\MenuValidator;
use Ekyna\Bundle\CmsBundle\Validator\Constraints\PageValidator;
use Ekyna\Bundle\CmsBundle\Validator\Constraints\SlideValidator;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Page validator
        ->set('ekyna_cms.validator.page', PageValidator::class)
            ->args([
                service('ekyna_cms.helper.routing'),
                service('ekyna_ui.helper.twig'),
                abstract_arg('The available page cms controllers'),
                param('ekyna_resource.locales'),
            ])
            ->tag('validator.constraint_validator')

        // Menu validator
        ->set('ekyna_cms.validator.menu', MenuValidator::class)
            ->args([
                param('ekyna_resource.locales'),
            ])
            ->tag('validator.constraint_validator')

        // Slide validator
        ->set('ekyna_cms.validator.slide', SlideValidator::class)
            ->args([
                service('ekyna_cms.slide_show.registry'),
            ])
            ->tag('validator.constraint_validator')

        // Block validator
        ->set('ekyna_cms.validator.block', BlockValidator::class)
            ->args([
                service('ekyna_cms.editor.plugin_registry'),
            ])
            ->tag('validator.constraint_validator')

        // Container validator
        ->set('ekyna_cms.validator.container', ContainerValidator::class)
            ->args([
                service('ekyna_cms.editor.plugin_registry'),
                service('ekyna_cms.repository.container'),
            ])
            ->tag('validator.constraint_validator')
    ;
};
