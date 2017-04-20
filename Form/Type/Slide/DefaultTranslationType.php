<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Slide;

use Ekyna\Bundle\CmsBundle\Entity\SlideTranslation;
use Ekyna\Bundle\UiBundle\Form\Type\TinymceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class DefaultTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Slide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class DefaultTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'         => t('field.title', [], 'EkynaUi'),
                'property_path' => 'data[title]',
            ])
            ->add('content', TinymceType::class, [
                'label'         => t('field.content', [], 'EkynaUi'),
                'property_path' => 'data[content]',
                'theme'         => 'light',
            ])
            ->add('button_label', TextType::class, [
                'label'         => t('slide.type.default.button_label', [], 'EkynaCms'),
                'property_path' => 'data[button_label]',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SlideTranslation::class);
    }
}
