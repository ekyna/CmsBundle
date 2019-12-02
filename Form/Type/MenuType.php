<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Doctrine\ORM\EntityRepository;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Ekyna\Bundle\CoreBundle\Form\Type\KeyValueCollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class MenuType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuType extends ResourceFormType
{
    /**
     * @var string
     */
    protected $pageClass;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorization;


    /**
     * Constructor.
     *
     * @param AuthorizationCheckerInterface $authorization
     * @param string                        $menuClass
     * @param string                        $pageClass
     */
    public function __construct(AuthorizationCheckerInterface $authorization, $menuClass, $pageClass)
    {
        parent::__construct($menuClass);

        $this->authorization = $authorization;
        $this->pageClass     = $pageClass;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', Type\TextareaType::class, [
                'label'        => 'ekyna_core.field.description',
                'admin_helper' => 'CMS_MENU_DESCRIPTION',
                'required'     => false,
            ]);

        if ($this->authorization->isGranted('ROLE_SUPER_ADMIN')) {
            $builder->add('attributes', KeyValueCollectionType::class, [
                'label'           => 'ekyna_core.field.attributes',
                'admin_helper'    => 'CMS_MENU_ATTRIBUTES',
                'add_button_text' => 'ekyna_core.button.add_attribute',
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \Ekyna\Bundle\CmsBundle\Entity\Menu $menu */
            $menu   = $event->getData();
            $form   = $event->getForm();
            $locked = $menu->isLocked();

            $form
                ->add('name', Type\TextType::class, [
                    'label'        => 'ekyna_core.field.name',
                    'admin_helper' => 'CMS_MENU_NAME',
                    'required'     => true,
                    'disabled'     => $locked,
                ])
                ->add('parent', EntityType::class, [
                    'label'         => 'ekyna_core.field.parent',
                    'admin_helper'  => 'CMS_MENU_PARENT',
                    'choice_label'  => 'title',
                    'required'      => true,
                    'disabled'      => $locked,
                    'class'         => $this->dataClass,
                    'query_builder' => function (EntityRepository $er) use ($menu) {
                        $qb = $er->createQueryBuilder('m');
                        $qb->addOrderBy('m.left', 'ASC');
                        if (null !== $parent = $menu->getParent()) {
                            $qb
                                ->andWhere('m.root = :root')
                                ->setParameter('root', $parent->getRoot());
                        }
                        if (0 < $menu->getId()) {
                            $qb
                                ->andWhere('m.id != :id')
                                ->setParameter('id', $menu->getId());
                        }

                        return $qb;
                    },
                ])
                ->add('enabled', Type\CheckboxType::class, [
                    'label'        => 'ekyna_core.field.enabled',
                    'admin_helper' => 'CMS_MENU_ENABLED',
                    'required'     => false,
                    'disabled'     => $locked,
                    'attr'         => [
                        'align_with_widget' => true,
                    ],
                ])
                ->add('translations', TranslationsFormsType::class, [
                    'form_type'    => MenuTranslationType::class,
                    'form_options' => [
                        'locked' => $locked,
                    ],
                    'label'        => false,
                    'attr'         => [
                        'widget_col' => 12,
                    ],
                ]);

            if ($this->authorization->isGranted('ROLE_SUPER_ADMIN')) {
                $form
                    ->add('route', Type\TextType::class, [
                        'label'        => 'ekyna_core.field.route',
                        'admin_helper' => 'CMS_MENU_ROUTE',
                        'required'     => false,
                        'disabled'     => $locked,
                    ])
                    ->add('parameters', KeyValueCollectionType::class, [
                        'label'           => 'ekyna_core.field.parameters',
                        'admin_helper'    => 'CMS_MENU_PARAMETERS',
                        'add_button_text' => 'ekyna_core.button.add_parameter',
                    ]);
            }

            if (!$locked) {
                $form
                    ->add('page', EntityType::class, [
                        'label'         => 'ekyna_cms.page.label.singular',
                        'admin_helper'  => 'CMS_MENU_PAGE',
                        'choice_label'  => 'name',
                        'placeholder'   => 'ekyna_core.value.choose',
                        'required'      => false,
                        'class'         => $this->pageClass,
                        'query_builder' => function (EntityRepository $er) {
                            $qb = $er->createQueryBuilder('p');
                            $qb
                                ->andWhere($qb->expr()->eq('p.dynamicPath', ':dynamic'))
                                ->setParameter('dynamic', false)
                                ->addOrderBy('p.left', 'ASC');

                            return $qb;
                        },
                    ]);
            }
        });
    }
}
