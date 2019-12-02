<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Ekyna\Bundle\CmsBundle\Model\Themes;
use Ekyna\Bundle\CoreBundle\Form\Type\FAIconChoiceType;
use Ekyna\Bundle\ResourceBundle\Form\Type\ConstantChoiceType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class TagType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TagType extends ResourceFormType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\TextType::class, [
                'label'        => 'ekyna_core.field.name',
                'admin_helper' => 'CMS_TAG_NAME',
            ])
            ->add('theme', ConstantChoiceType::class, [
                'label'        => 'ekyna_core.field.theme',
                'admin_helper' => 'CMS_TAG_THEME',
                'class'        => Themes::class,
            ])
            ->add('icon', FAIconChoiceType::class, [
                'required' => false,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_tag';
    }
}
