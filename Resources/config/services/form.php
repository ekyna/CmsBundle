<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Form\CreateSlideFlow;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\BaseBlockTranslationType;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\BaseBlockType;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\BaseContainerType;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\MediaChoiceType;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\TabType;
use Ekyna\Bundle\CmsBundle\Form\Type\MenuType;
use Ekyna\Bundle\CmsBundle\Form\Type\PageType;
use Ekyna\Bundle\CmsBundle\Form\Type\SeoType;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\ImageType;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\ThemeType;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\TypeType;
use Ekyna\Bundle\CmsBundle\Form\Type\SlideType;
use Ekyna\Bundle\CmsBundle\Form\Type\TagChoiceType;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Menu form type
        ->set('ekyna_cms.form_type.menu', MenuType::class)
            ->args([
                service('security.authorization_checker'),
                param('ekyna_cms.class.page'),
            ])
            ->tag('form.type')

        // Page form type
        ->set('ekyna_cms.form_type.page', PageType::class)
            ->args([
                abstract_arg('Page configuration'),
            ])
            ->tag('form.type')

        // Seo form type
        ->set('ekyna_cms.form_type.seo', SeoType::class)
            ->args([
                service('ekyna_cms.factory.seo'),
            ])
            ->tag('form.type')

        // Tag choice form type
        ->set('ekyna_cms.form_type.tag_choice', TagChoiceType::class)
            ->tag('form.type')
            ->tag('form.js', ['selector' => '.cms-tag-choice', 'path' => 'ekyna-cms/form/tag-choice'])

        // Slide form type
        ->set('ekyna_cms.form_type.slide', SlideType::class)
            ->args([
                service('ekyna_cms.slide_show.registry'),
            ])
            ->tag('form.type')

        // Create slide form flow
        ->set('ekyna_cms.form_flow.create_slide', CreateSlideFlow::class)
            ->parent('craue.form.flow')

        // Slide type form type
        ->set('ekyna_cms.form_type.slide_type', TypeType::class) // TODO Rename to TypeChoiceType
            ->args([
                service('ekyna_cms.slide_show.registry'),
                service('ekyna_cms.factory.slide'),
                service('translator'),
            ])
            ->tag('form.type')
            ->tag('form.js', ['selector' => '.cms-slide-type', 'path' => 'ekyna-cms/form/slide-type'])

        // Slide theme form type
        ->set('ekyna_cms.form_type.slide_theme', ThemeType::class) // TODO Rename to ThemeChoiceType
            ->args([
                abstract_arg('Slide themes configuration'),
            ])
            ->tag('form.type')

        // Slide image form type
        ->set('ekyna_cms.form_type.slide_image', ImageType::class) // TODO Rename to ImageChoiceType
            ->args([
                service('ekyna_media.repository.media'),
            ])
            ->tag('form.type')
    ;

    // ---------------- Editor ------------------

    $container
        ->services()

        // Editor base block form type
        ->set('ekyna_cms.form_type.editor.block.base', BaseBlockType::class)
            ->args([
                param('ekyna_cms.class.block'),
            ])
            ->tag('form.type')

        // Editor base block form type
        ->set('ekyna_cms.form_type.editor.block.base_translation', BaseBlockTranslationType::class)
            ->args([
                param('ekyna_cms.class.block_translation'),
            ])
            ->tag('form.type')

        // Editor tab block form type
        ->set('ekyna_cms.form_type.editor.block.tab', TabType::class)
            ->args([
                service('ekyna_resource.provider.locale'),
            ])
            ->tag('form.type')

        // Editor base container form type
        ->set('ekyna_cms.form_type.editor.container.base', BaseContainerType::class)
            ->args([
                param('ekyna_cms.class.container'),
            ])
            ->tag('form.type')

        // Editor media choice form type
        ->set('ekyna_cms.form_type.editor.media_choice', MediaChoiceType::class)
            ->args([
                service('ekyna_media.repository.media'),
            ])
            ->tag('form.type')
    ;
};
