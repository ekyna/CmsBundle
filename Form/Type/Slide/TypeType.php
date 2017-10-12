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
 * Class TypeType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Slide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TypeType extends AbstractType
{
    /**
     * @var TypeRegistryInterface
     */
    private $registry;

    /**
     * @var ResourceRepositoryInterface
     */
    private $repository;


    /**
     * Constructor.
     *
     * @param TypeRegistryInterface       $registry
     * @param ResourceRepositoryInterface $repository
     */
    public function __construct(
        TypeRegistryInterface $registry,
        ResourceRepositoryInterface $repository
    ) {
        $this->registry = $registry;
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['disabled']) {
            return;
        }

        $slideShow = new SlideShow();

        foreach ($this->registry->all() as $type) {
            /** @var Slide $slide */
            $slide = $this->repository->createNew();
            $type->buildExample($slide);
            $slideShow->addSlide($slide);
        }

        $view->vars['slide_show'] = $slideShow;

        FormUtil::addClass($view, 'cms-slide-type');
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = [];

        foreach ($this->registry->all() as $type) {
            $choices[$type->getLabel()] = $type->getName();
        }

        $resolver->setDefaults([
            'label'   => 'ekyna_core.field.type',
            'choices' => $choices,
            'select2' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_slide_type';
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
