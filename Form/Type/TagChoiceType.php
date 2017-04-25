<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagChoiceType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TagChoiceType extends AbstractType
{
    /**
     * @var string
     */
    private $tagClass;


    /**
     * Constructor.
     *
     * @param string $tagClass
     */
    public function __construct($tagClass)
    {
        $this->tagClass = $tagClass;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label'       => function (Options $options, $value) {
                if (!empty($value)) {
                    return $value;
                }

                return 'ekyna_cms.tag.label.' . ($options['multiple'] ? 'plural' : 'singular');
            },
            'class'       => $this->tagClass,
            'select2'     => false,
            'choice_attr' => function ($tag) {
                /** @var \Ekyna\Bundle\CmsBundle\Model\TagInterface $tag */
                return [
                    'data-icon'  => $tag->getIcon(),
                    'data-theme' => $tag->getTheme(),
                ];
            },
            'attr'        => [
                'class' => 'cms-tag-choice',
            ],
            'allow_new' => true,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return ResourceType::class;
    }
}
