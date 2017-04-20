<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Controller\CmsController;
use Ekyna\Bundle\CmsBundle\Controller\Editor\AbstractController;
use Ekyna\Bundle\CmsBundle\Controller\Editor\BlockController;
use Ekyna\Bundle\CmsBundle\Controller\Editor\ContainerController;
use Ekyna\Bundle\CmsBundle\Controller\Editor\ContentController;
use Ekyna\Bundle\CmsBundle\Controller\Editor\EditorController;
use Ekyna\Bundle\CmsBundle\Controller\Editor\RowController;
use Ekyna\Bundle\CmsBundle\Controller\SlideShowController;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Cms controller
        ->set('ekyna_cms.controller.cms', CmsController::class)
            ->args([
                service('twig'),
                service('ekyna_resource.search'),
                service('request_stack'),
            ])
            ->alias(CmsController::class, 'ekyna_cms.controller.cms')->public()

        // Slide show controller
        ->set('ekyna_cms.controller.slide_show', SlideShowController::class)
            ->args([
                service('ekyna_cms.slide_show.registry'),
            ])
            ->alias(SlideShowController::class, 'ekyna_cms.controller.slide_show')->public()

        // Editor abstract controller
        ->set('ekyna_cms.controller.editor.abstract', AbstractController::class)
            ->abstract()
            ->args([
                service('ekyna_cms.editor.editor'),
                service('doctrine.orm.entity_manager'),
                service('validator'),
                service('serializer'),
                service('ekyna_ui.modal.renderer'),
                param('kernel.debug'),
            ])

        // Content controller
        ->set('ekyna_cms.controller.editor.content', ContentController::class)
            ->parent('ekyna_cms.controller.editor.abstract')
            ->public()

        // Container controller
        ->set('ekyna_cms.controller.editor.container', ContainerController::class)
            ->parent('ekyna_cms.controller.editor.abstract')
            ->public()

        // Row controller
        ->set('ekyna_cms.controller.editor.row', RowController::class)
            ->parent('ekyna_cms.controller.editor.abstract')
            ->public()

        // Block controller
        ->set('ekyna_cms.controller.editor.block', BlockController::class)
            ->parent('ekyna_cms.controller.editor.abstract')
            ->public()

        // Editor controller
        ->set('ekyna_cms.controller.editor', EditorController::class)
            ->args([
                service('ekyna_cms.editor.editor'),
                service('twig'),
                service('router'),
                service('ekyna_cms.repository.page'),
                param('ekyna_cms.home_route'),
            ])
            ->public()
    ;
};
