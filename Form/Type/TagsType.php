<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagsType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TagsType extends AbstractType
{
    /**
     * @var string
     */
    private $tagClass;

    /**
     * Constructor.
     *
     * @param string $tagClass
     */
    public function __construct($tagClass)
    {
        $this->tagClass = $tagClass;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label' => 'ekyna_core.field.tags',
                'class' => $this->tagClass,
                'multiple' => true,
                'property' => 'name',
                'allow_new' => true,
                'allow_list' => true,
                'empty_value' => 'ekyna_core.field.tags',
                'attr' => [
                    'placeholder' => 'ekyna_core.field.tags',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ekyna_resource';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_tags';
    }
}
