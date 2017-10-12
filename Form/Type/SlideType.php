<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\TypeType;
use Ekyna\Bundle\CmsBundle\SlideShow\TypeRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class SlideType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideType extends ResourceFormType
{
    /**
     * @var TypeRegistryInterface
     */
    private $registry;

    /**
     * Constructor.
     *
     * @param TypeRegistryInterface $registry
     */
    public function __construct(TypeRegistryInterface $registry, $class)
    {
        parent::__construct($class);

        $this->registry = $registry;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'ekyna_core.field.name',
            ])
            ->add('type', TypeType::class, [
                'disabled'    => !$options['type_mode'],
                'constraints' => [
                    new NotNull(),
                ],
            ]);

        if (!$options['type_mode']) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $slide = $event->getData();
                $form = $event->getForm();

                if (null === $slide) {
                    throw new \LogicException("Form data must be set.");
                }
                if (!$slide instanceof Slide) {
                    throw new \InvalidArgumentException("Expected instance of " . Slide::class);
                }
                if (null === $typeName = $slide->getType()) {
                    throw new \LogicException("Slide's type must be set.");
                }

                $type = $this->registry->get($slide->getType());
                $type->buildForm($form);
            });
        }
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('type_mode', false)
            ->setAllowedTypes('type_mode', 'bool');
    }
}
