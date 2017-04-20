<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model\TabsTranslation;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaChoiceType;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\UiBundle\Form\Type\TinymceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class TabsBlockTabsTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabsTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'              => t('field.title', [], 'EkynaUi'),
            ])
            ->add('media', MediaChoiceType::class, [
                'label'              => t('field.media', [], 'EkynaUi'),
                'types'              => [MediaTypes::VIDEO, MediaTypes::IMAGE, MediaTypes::SVG],
                'required'           => false,
            ])
            ->add('content', TinymceType::class, [
                'label'              => t('field.content', [], 'EkynaUi'),
                'theme'              => 'light',
            ])
            ->add('buttonLabel', TextType::class, [
                'label'    => 'Button label', // TODO
                'required' => false,
            ])
            ->add('buttonUrl', TextType::class, [
                'label'    => 'Button url', // TODO
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', TabsTranslation::class);
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_block_tabs_translation';
    }
}
