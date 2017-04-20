<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class TemplateBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TemplateBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->get('data')
            ->add('content', ChoiceType::class, [
                'label'                     => t('field.content', [], 'EkynaUi'),
                'choices'                   => $options['templates'],
                'choice_translation_domain' => false,
                'select2'                   => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('templates')
            ->setAllowedTypes('templates', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_block_template';
    }

    public function getParent(): ?string
    {
        return BaseBlockType::class;
    }
}
