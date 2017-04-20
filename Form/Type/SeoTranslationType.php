<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\CmsBundle\Entity\SeoTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class SeoTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SeoTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', Type\TextType::class, [
                'label'        => t('field.title', [], 'EkynaUi'),
                'required'     => true,
                'admin_helper' => 'CMS_SEO_TITLE',
            ])
            ->add('description', Type\TextareaType::class, [
                'label'        => t('field.description', [], 'EkynaUi'),
                'required'     => true,
                'admin_helper' => 'CMS_SEO_DESCRIPTION',
            ])
            ->add('keywords', Type\TextType::class, [
                'label'        => t('field.keywords', [], 'EkynaUi'),
                'required'     => false,
                'admin_helper' => 'CMS_SEO_KEYWORDS',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SeoTranslation::class);
    }
}
