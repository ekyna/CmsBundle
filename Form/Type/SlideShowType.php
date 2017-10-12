<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SlideShowType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideShowType extends ResourceFormType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'ekyna_core.field.name',
        ]);
    }
}
