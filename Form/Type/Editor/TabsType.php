<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model\Tabs;
use Ekyna\Bundle\UiBundle\Form\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class TabsBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme', Type\ChoiceType::class, [
                'label'                     => t('block.field.theme', [], 'EkynaCms'),
                'choices'                   => array_flip($options['themes']),
                'choice_translation_domain' => false,
                'placeholder'               => 'value.none',
                'required'                  => true,
                'select2'                   => false,
            ])
            ->add('align', Type\ChoiceType::class, [
                'label'                     => t('block.field.align', [], 'EkynaCms'),
                'choices'                   => [
                    'Left'   => 'left',
                    'Right'  => 'right',
                    'Center' => 'center',
                ],
                'choice_translation_domain' => false,
                'placeholder'               => t('value.none', [], 'EkynaUi'),
                'required'                  => true,
                'select2'                   => false,
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
                'add_button_text' => t('button.add', [], 'EkynaUi'),
                'sub_widget_col'  => 10,
                'button_col'      => 2,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('data_class', Tabs::class)
            ->setDefault('themes', [])
            ->setAllowedTypes('themes', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_block_tabs';
    }
}
