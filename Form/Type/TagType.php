<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Ekyna\Bundle\CmsBundle\Model\ChangeFrequencies;
use Ekyna\Bundle\CmsBundle\Model\Themes;
use Ekyna\Bundle\CoreBundle\Form\Type\FAIconChoiceType;
use Ekyna\Bundle\CoreBundle\Model\FAIcons;
use Ekyna\Bundle\ResourceBundle\Form\Type\ConstantChoiceType;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('icon', FAIconChoiceType::class, [
                'required' => false,
            ])
            ->add('theme', ConstantChoiceType::class, [
                'label'        => 'ekyna_core.field.theme',
                'admin_helper' => 'CMS_TAG_THEME',
                'class'        => Themes::class
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
