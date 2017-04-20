<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\CmsBundle\Entity\MenuTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class MenuTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'        => t('field.title', [], 'EkynaUi'),
                'admin_helper' => 'CMS_MENU_TITLE',
                'required'     => true,
            ])
            ->add('path', TextType::class, [
                'label'        => t('field.url', [], 'EkynaUi'),
                'admin_helper' => 'CMS_MENU_PATH',
                'required'     => false,
                'disabled'     => $options['locked'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => MenuTranslation::class,
                'locked'     => false,
            ]);
    }
}
