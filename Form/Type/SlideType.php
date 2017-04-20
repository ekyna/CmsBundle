<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\TypeType;
use Ekyna\Bundle\CmsBundle\SlideShow\TypeRegistryInterface;
use Ekyna\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

use function Symfony\Component\Translation\t;

/**
 * Class SlideType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideType extends AbstractResourceType
{
    private TypeRegistryInterface $registry;

    public function __construct(TypeRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => t('field.name', [], 'EkynaUi'),
            ])
            ->add('type', TypeType::class, [
                'disabled'    => !$options['type_mode'],
                'constraints' => [
                    new NotNull(),
                ],
            ]);

        if (!$options['type_mode']) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $slide = $event->getData();
                $form = $event->getForm();

                if (null === $slide) {
                    throw new LogicException('Form data must be set.');
                }
                if (!$slide instanceof Slide) {
                    throw new InvalidArgumentException('Expected instance of ' . Slide::class);
                }
                if (null === $typeName = $slide->getType()) {
                    throw new LogicException('Slide\'s type must be set.');
                }

                $type = $this->registry->get($typeName);
                $type->buildForm($form);
            });
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('type_mode', false)
            ->setAllowedTypes('type_mode', 'bool');
    }
}
