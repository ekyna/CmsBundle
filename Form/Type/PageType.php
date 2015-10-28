<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class PageType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageType extends ResourceFormType
{
    /**
     * @var array
     */
    protected $config;


    /**
     * Constructor.
     *
     * @param string $class
     * @param array  $config
     */
    public function __construct($class, array $config)
    {
        parent::__construct($class);

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('seo', 'ekyna_cms_seo')
            ->add('translations', 'a2lix_translationsForms', array(
                'form_type'      => new PageTranslationType(),
                'label'          => false,
                'error_bubbling' => false,
                'attr'           => array(
                    'widget_col' => 12,
                ),
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page */
            $page = $event->getData();
            $form = $event->getForm();

            if ($page->getStatic()) {
                $form
                    ->add('name', 'text', array(
                        'label'    => 'ekyna_core.field.name',
                        'admin_helper' => 'CMS_PAGE_NAME',
                        'disabled' => true,
                    ))
                    ->add('parent', 'entity', array(
                        'label'       => 'ekyna_core.field.parent',
                        'admin_helper' => 'CMS_PAGE_PARENT',
                        'class'       => $this->dataClass,
                        'property'    => 'name',
                        'empty_value' => 'ekyna_cms.page.value.root',
                        'disabled'    => true,
                    ))
                    ->add('enabled', 'checkbox', array(
                        'label'    => 'ekyna_core.field.enabled',
                        'required' => false,
                        'disabled' => true,
                        'attr'     => array(
                            'align_with_widget' => true,
                        ),
                    ))
                ;
            } else {
                $form
                    ->add('name', 'text', array(
                        'label'    => 'ekyna_core.field.name',
                        'admin_helper' => 'CMS_PAGE_PATH',
                        'required' => true,
                    ))
                    ->add('parent', 'entity', array(
                        'label'         => 'ekyna_core.field.parent',
                        'admin_helper' => 'CMS_PAGE_PARENT',
                        'class'         => $this->dataClass,
                        'property'      => 'name',
                        'required'      => true,
                        'query_builder' => function (EntityRepository $er) use ($page) {
                            $qb = $er
                                ->createQueryBuilder('p')
                                ->where('p.locked = :locked')
                                ->orderBy('p.left', 'ASC')
                                ->setParameter('locked', false)
                            ;
                            if (0 < $page->getId()) {
                                $qb
                                    ->andWhere('p.id != :id')
                                    ->setParameter('id', $page->getId())
                                ;
                            }
                            return $qb;
                        },
                    ))
                    ->add('enabled', 'checkbox', array(
                        'label'    => 'ekyna_core.field.enabled',
                        'admin_helper' => 'CMS_PAGE_ENABLE',
                        'required' => false,
                        'attr'     => array(
                            'align_with_widget' => true,
                        ),
                    ))
                ;

                $controllers = [];
                foreach ($this->config['controllers'] as $name => $config) {
                    $controllers[$name] = $config['title'];
                }

                $form->add('controller', 'choice', array(
                    'label'    => 'ekyna_cms.page.field.controller',
                    'admin_helper' => 'CMS_PAGE_CONTROLLER',
                    'choices'  => $controllers,
                    'required' => true,
                ));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	return 'ekyna_cms_page';
    }
}
