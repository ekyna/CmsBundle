<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\CmsBundle\Model\TagInterface;
use Ekyna\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagChoiceType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TagChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'resource'    => 'ekyna_cms.tag',
            'required'    => false,
            'multiple'    => true,
            'choice_attr' => function ($tag) {
                /** @var TagInterface $tag */
                return [
                    'data-icon'  => $tag->getIcon(),
                    'data-theme' => $tag->getTheme(),
                ];
            },
            'attr'        => [
                'class' => 'cms-tag-choice',
            ],
            'allow_new'   => true,
        ]);
    }

    public function getParent(): ?string
    {
        return ResourceChoiceType::class;
    }
}
