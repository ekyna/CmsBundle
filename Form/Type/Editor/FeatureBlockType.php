<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

use function Symfony\Component\Translation\t;

/**
 * Class FeatureBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class FeatureBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->get('data')
            ->add('animation', AnimationType::class, [
                'animations' => $options['animations'],
            ])
            ->add('html_max_width', Type\TextType::class, [
                'label'       => t('block.field.max_width', [], 'EkynaCms'),
                'required'    => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+(px|%)$/'
                        // TODO message translation
                    ]),
                ],
            ])
            ->add('html_margin_top', Type\TextType::class, [
                'label'       => t('block.field.margin_top', [], 'EkynaCms'),
                'required'    => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+px$/'
                        // TODO message translation
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('animations')
            ->setAllowedTypes('animations', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_block_feature';
    }

    public function getParent(): ?string
    {
        return BaseBlockType::class;
    }
}
