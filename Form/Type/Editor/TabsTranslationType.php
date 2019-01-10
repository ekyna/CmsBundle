<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model\TabsTranslation;
use Ekyna\Bundle\CoreBundle\Form\Type\TinymceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TabsBlockTabsTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabsTranslationType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'ekyna_core.field.title',
            ])
            ->add('content', TinymceType::class, [
                'label' => 'ekyna_core.field.content',
                'theme' => 'front',
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

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', TabsTranslation::class);
    }
}
