<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Slide;

use Ekyna\Bundle\CmsBundle\Entity\SlideTranslation;
use Ekyna\Bundle\CoreBundle\Form\Type\TinymceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HeroTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Slide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class HeroTranslationType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'         => 'ekyna_core.field.title',
                'property_path' => 'data[title]',
            ])
            ->add('content', TinymceType::class, [
                'label'         => 'ekyna_core.field.content',
                'property_path' => 'data[content]',
                'theme'         => 'light',
            ])
            ->add('button_label', TextType::class, [
                'label'         => 'ekyna_cms.slide.type.default.button_label',
                'property_path' => 'data[button_label]',
                'required'      => false,
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', SlideTranslation::class);
    }
}
