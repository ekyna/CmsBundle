<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\CmsBundle\Model\Themes;
use Ekyna\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Ekyna\Bundle\ResourceBundle\Form\Type\ConstantChoiceType;
use Ekyna\Bundle\UiBundle\Form\Type\FAIconChoiceType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

use function Symfony\Component\Translation\t;

/**
 * Class TagType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TagType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class, [
                'label'        => t('field.name', [], 'EkynaUi'),
                'admin_helper' => 'CMS_TAG_NAME',
            ])
            ->add('theme', ConstantChoiceType::class, [
                'label'        => t('field.theme', [], 'EkynaUi'),
                'admin_helper' => 'CMS_TAG_THEME',
                'class'        => Themes::class,
            ])
            ->add('icon', FAIconChoiceType::class, [
                'required' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_tag';
    }
}
