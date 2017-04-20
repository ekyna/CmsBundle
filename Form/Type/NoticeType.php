<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\CmsBundle\Model\Themes;
use Ekyna\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Ekyna\Bundle\ResourceBundle\Form\Type\ConstantChoiceType;
use Ekyna\Bundle\UiBundle\Form\Type\FAIconChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use function Symfony\Component\Translation\t;

/**
 * Class NoticeType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class NoticeType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => t('field.name', [], 'EkynaUi'),
            ])
            ->add('theme', ConstantChoiceType::class, [
                'label' => t('field.theme', [], 'EkynaUi'),
                'class' => Themes::class,
            ])
            ->add('icon', FAIconChoiceType::class, [
                'required' => false,
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => t('field.start_date', [], 'EkynaUi'),
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => t('field.end_date', [], 'EkynaUi'),
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
