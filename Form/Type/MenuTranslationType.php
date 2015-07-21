<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MenuTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuTranslationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'ekyna_core.field.title',
                'required' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Ekyna\Bundle\CmsBundle\Entity\MenuTranslation',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_menu_translation';
    }
}
