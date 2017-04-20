<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Slide;

use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\CmsBundle\Entity\SlideShow;
use Ekyna\Bundle\CmsBundle\SlideShow\TypeRegistryInterface;
use Ekyna\Bundle\UiBundle\Form\Util\FormUtil;
use Ekyna\Component\Resource\Factory\ResourceFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\Translation\t;

/**
 * Class TypeType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Slide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TypeType extends AbstractType
{
    private TypeRegistryInterface    $registry;
    private ResourceFactoryInterface $factory;
    private TranslatorInterface      $translator;

    public function __construct(
        TypeRegistryInterface    $factory,
        ResourceFactoryInterface $repository,
        TranslatorInterface      $translator
    ) {
        $this->registry = $factory;
        $this->factory = $repository;
        $this->translator = $translator;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if ($options['disabled']) {
            return;
        }

        $slideShow = new SlideShow();

        foreach ($this->registry->all() as $type) {
            /** @var Slide $slide */
            $slide = $this->factory->create();
            $type->buildExample($slide);
            $slideShow->addSlide($slide);
        }

        $view->vars['slide_show'] = $slideShow;

        FormUtil::addClass($view, 'cms-slide-type');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $choices = [];

        foreach ($this->registry->all() as $type) {
            $label = $this->translator->trans($type->getLabel(), [], $type->getDomain());

            $choices[$label] = $type->getName();
        }

        $resolver->setDefaults([
            'label'                     => t('field.type', [], 'EkynaUi'),
            'choices'                   => $choices,
            'choice_translation_domain' => false,
            'select2'                   => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_slide_type';
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
