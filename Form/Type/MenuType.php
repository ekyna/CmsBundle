<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class MenuType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuType extends ResourceFormType
{
    /**
     * @var string
     */
    protected $pageClass;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;


    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param string                   $menuClass
     * @param string                   $pageClass
     */
    public function __construct(SecurityContextInterface $securityContext, $menuClass, $pageClass)
    {
        parent::__construct($menuClass);

        $this->securityContext = $securityContext;
        $this->pageClass = $pageClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translationsForms', array(
                'form_type' => new MenuTranslationType(),
                'label'     => false,
                'attr' => array(
                    'widget_col' => 12,
                ),
            ))
            ->add('description', 'textarea', array(
                'label'    => 'ekyna_core.field.description',
                'required' => false
            ))
        ;

        if ($this->securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $builder->add('attributes', 'ekyna_key_value_collection', array(
                'label'           => 'ekyna_core.field.attributes',
                'add_button_text' => 'ekyna_core.button.add_attribute',
            ));
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            /** @var \Ekyna\Bundle\CmsBundle\Entity\Menu $menu */
            $menu = $event->getData();
            $form = $event->getForm();
            $disabled = $menu->getLocked();

            $form
                ->add('name', 'text', array(
                    'label'    => 'ekyna_core.field.name',
                    'required' => true,
                    'disabled' => $disabled,
                ))
                ->add('parent', 'entity', array(
                    'label'         => 'ekyna_core.field.parent',
                    'property'      => 'title',
                    'required'      => true,
                    'disabled'      => $disabled,
                    'class'         => $this->dataClass,
                    'query_builder' => function(EntityRepository $er) use ($menu) {
                        $qb = $er->createQueryBuilder('m');
                        $qb->addOrderBy('m.left', 'ASC');
                        if (null !== $parent = $menu->getParent()) {
                            $qb
                                ->andWhere('m.root = :root')
                                ->setParameter('root', $parent->getRoot())
                            ;
                        }
                        if (0 < $menu->getId()) {
                            $qb
                                ->andWhere('m.id != :id')
                                ->setParameter('id', $menu->getId())
                            ;
                        }
                        return $qb;
                    },
                ))
                ->add('path', 'text', array(
                    'label'        => 'ekyna_core.field.url',
                    'admin_helper' => 'CMS_MENU_PATH',
                    'required'     => false,
                    'disabled'     => $disabled,
                ))
            ;

            if ($this->securityContext->isGranted('ROLE_SUPER_ADMIN')) {
                $form
                    ->add('route', 'text', array(
                        'label'        => 'ekyna_core.field.route',
                        'admin_helper' => 'CMS_MENU_ROUTE',
                        'required'     => false,
                        'disabled'     => $disabled,
                    ))
                    ->add('parameters', 'ekyna_key_value_collection', array(
                        'label'           => 'ekyna_core.field.parameters',
                        'add_button_text' => 'ekyna_core.button.add_parameter',
                    ))
                ;
            }

            if (!$disabled) {
                $form
                    ->add('page', 'entity', array(
                        'label'         => 'ekyna_cms.page.label.singular',
                        'admin_helper'  => 'CMS_MENU_PAGE',
                        'property'      => 'name',
                        'required'      => false,
                        'class'         => $this->pageClass,
                        'query_builder' => function (EntityRepository $er) {
                            $qb = $er->createQueryBuilder('p');
                            $qb
                                ->andWhere($qb->expr()->eq('p.dynamicPath', ':dynamic'))
                                ->setParameter('dynamic', false)
                                ->addOrderBy('p.left', 'ASC')
                            ;
                            return $qb;
                        },
                    ))
                ;
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_menu';
    }
}
