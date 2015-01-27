<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
     * Constructor.
     *
     * @param string $menuClass
     * @param string $pageClass
     */
    public function __construct($menuClass, $pageClass)
    {
        parent::__construct($menuClass);

        $this->pageClass = $pageClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'ekyna_core.field.title',
                'required' => true
            ))
            ->add('description', 'textarea', array(
                'label' => 'ekyna_core.field.description',
                'required' => false
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            /** @var \Ekyna\Bundle\CmsBundle\Entity\Menu $menu */
            $menu = $event->getData();
            $form = $event->getForm();
            $disabled = $menu->getLocked();

            $form
                ->add('name', 'text', array(
                    'label' => 'ekyna_core.field.name',
                    'required' => true,
                    'disabled' => $disabled,
                ))
                ->add('parent', 'entity', array(
                    'label' => 'ekyna_core.field.parent',
                    'class' => $this->dataClass,
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
                    'property' => 'title',
                    'required' => true,
                    'disabled' => $disabled,
                ))
                ->add('path', 'text', array(
                    'label' => 'ekyna_core.field.url',
                    'admin_helper' => 'CMS_MENU_PATH',
                    'required' => false,
                    'disabled' => $disabled,
                ))
                ->add('route', 'text', array(
                    'label' => 'ekyna_core.field.route',
                    'admin_helper' => 'CMS_MENU_ROUTE',
                    'required' => false,
                    'disabled' => $disabled,
                ))
                // TODO parameters ?
            ;

            if (!$disabled) {
                $form
                    ->add('page', 'entity', array(
                        'label' => 'ekyna_cms.page.label.singular',
                        'admin_helper' => 'CMS_MENU_PAGE',
                        'class' => $this->pageClass,
                        'query_builder' => function (EntityRepository $er) {
                            $qb = $er->createQueryBuilder('p');
                            $qb
                                ->andWhere($qb->expr()->eq('p.dynamicPath', $qb->expr()->literal(false)))
                                ->addOrderBy('p.left', 'ASC')
                            ;
                            return $qb;
                        },
                        'property' => 'name',
                        'required' => false,
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
