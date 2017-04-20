<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Slide;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class ThemeType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Slide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ThemeType extends AbstractType
{
    private array $choices;

    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label'                     => t('field.theme', [], 'EkynaUi'),
            'choices'                   => array_flip($this->choices),
            'choice_translation_domain' => false,
            'select2'                   => false,
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
