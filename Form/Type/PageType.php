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
                'form_type' => new PageTranslationType(),
                'label'     => false,
                'error_bubbling' => false,
                'attr' => array(
                    'widget_col' => 12,
                ),
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $page = $event->getData();
            $form = $event->getForm();

            if ($page->getStatic()) {
                $form->add('name', 'text', array(
                    'label' => 'ekyna_core.field.name',
                	'disabled' => true,
                ));
                $form->add('parent', 'entity', array(
                    'label' => 'ekyna_core.field.parent',
                	'class' => $this->dataClass,
                    'property' => 'name',
                    'empty_value' => 'Racine',
                	'disabled' => true,
                ));
            } else {
                $form
                    ->add('name', 'text', array(
                        'label' => 'ekyna_core.field.name',
                        'required' => true,
                    ))
                    ->add('parent', 'entity', array(
                        'label' => 'ekyna_core.field.parent',
                        'class' => $this->dataClass,
                        'query_builder' => function(EntityRepository $er) use ($page) {
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
                        'property' => 'name',
                        'required' => true,
                    ))
                ;

                $controllers = [];
                foreach ($this->config['controllers'] as $name => $config) {
                    $controllers[$name] = $config['title'];
                }

                $form->add('controller', 'choice', array(
                    'label' => 'ekyna_cms.page.field.controller',
                    'choices' => $controllers,
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
