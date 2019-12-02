<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\CmsBundle\Entity\MenuTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MenuTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuTranslationType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'        => 'ekyna_core.field.title',
                'admin_helper' => 'CMS_MENU_TITLE',
                'required'     => true,
            ])
            ->add('path', TextType::class, [
                'label'        => 'ekyna_core.field.url',
                'admin_helper' => 'CMS_MENU_PATH',
                'required'     => false,
                'disabled'     => $options['locked'],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => MenuTranslation::class,
                'locked'     => false,
            ]);
    }
}
