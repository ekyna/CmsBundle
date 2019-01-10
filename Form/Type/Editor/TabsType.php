<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model\Tabs;
use Ekyna\Bundle\CoreBundle\Form\Type\CollectionType;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaChoiceType;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TabsBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabsType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('theme', Type\ChoiceType::class, [
                'label'       => 'ekyna_cms.block.field.theme',
                'choices'     => array_flip($options['themes']),
                'placeholder' => 'ekyna_core.value.none',
                'required'    => false,
                'select2'     => false,
            ])
            ->add('align', Type\ChoiceType::class, [
                'label'       => 'ekyna_cms.block.field.align',
                'choices'     => [
                    'Left'   => 'left',
                    'Right'  => 'right',
                    'Center' => 'center',
                ],
                'placeholder' => 'ekyna_core.value.none',
                'required'    => false,
                'select2'     => false,
            ])
            ->add('media', MediaChoiceType::class, [
                'label'    => 'ekyna_core.field.media',
                'types'    => [MediaTypes::VIDEO, MediaTypes::IMAGE, MediaTypes::SVG],
                'required' => false,
            ])
            ->add('translations', TranslationsFormsType::class, [
                'form_type'      => TabsTranslationType::class,
                'label'          => false,
                'error_bubbling' => false,
            ])
            ->add('tabs', CollectionType::class, [
                'label'           => 'Tabs', // TODO
                'by_reference'    => true,
                'entry_type'      => TabType::class,
                'allow_add'       => true,
                'allow_delete'    => true,
                'allow_sort'      => true,
                'add_button_text' => 'ekyna_core.button.add',
                'sub_widget_col'  => 10,
                'button_col'      => 2,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('data_class', Tabs::class)
            ->setDefault('themes', [])
            ->setAllowedTypes('themes', 'array');
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_block_tabs';
    }
}
