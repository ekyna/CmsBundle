<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Slide;

use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\CmsBundle\Entity\SlideShow;
use Ekyna\Bundle\CmsBundle\SlideShow\TypeRegistryInterface;
use Ekyna\Bundle\CoreBundle\Form\Util\FormUtil;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ThemeType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Slide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ThemeType extends AbstractType
{
    /**
     * @var array
     */
    private $choices;


    /**
     * Constructor.
     *
     * @param array $choices
     */
    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label'   => 'ekyna_core.field.theme',
            'choices' => array_flip($this->choices),
            'select2' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
