<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Ekyna\Bundle\CmsBundle\Model\Themes;
use Ekyna\Bundle\CoreBundle\Form\Type\FAIconChoiceType;
use Ekyna\Bundle\ResourceBundle\Form\Type\ConstantChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class NoticeType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class NoticeType extends ResourceFormType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'ekyna_core.field.name',
            ])
            ->add('theme', ConstantChoiceType::class, [
                'label' => 'ekyna_core.field.theme',
                'class' => Themes::class,
            ])
            ->add('icon', FAIconChoiceType::class, [
                'required' => false,
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => 'ekyna_core.field.start_date',
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => 'ekyna_core.field.end_date',
            ])
            ->add('translations', TranslationsFormsType::class, [
                'form_type'      => NoticeTranslationType::class,
                'label'          => false,
                'error_bubbling' => false,
                'attr'           => [
                    'widget_col' => 12,
                ],
            ]);
    }
}
