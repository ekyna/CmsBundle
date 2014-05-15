<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * PageType
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageType extends AbstractType
{
    protected $dataClass;
    protected $contentEnabled;

    public function __construct($class, $contentEnabled)
    {
        $this->dataClass = $class;
        $this->contentEnabled = $contentEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'ekyna_core.field.title'
            ))
            ->add('seo', 'ekyna_cms_seo', array(
                'label' => false
            ))
            ->add('menu', 'checkbox', array(
                'label' => 'ekyna_cms.field.show_main_menu',
                'required' => false,
                'attr' => array('align_with_widget' => true)
            ))
            ->add('footer', 'checkbox', array(
                'label' => 'ekyna_cms.field.show_footer_menu',
                'required' => false,
                'attr' => array('align_with_widget' => true)
            ))
        ;

        if(!$this->contentEnabled) {
            $builder
                ->add('html', 'textarea', array(
                    'label' => 'ekyna_core.field.content',
                    'attr' => array(
                	    'class' => 'tinymce',
                        'data-theme' => 'advanced',
                    )
                ))
            ;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $page = $event->getData();
            $form = $event->getForm();

            if($page->getStatic()) {
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
                ))
                ->add('path', 'text', array(
                    'label' => 'ekyna_core.field.url',
                    'disabled' => true,
                ));
            }else{
                $form->add('name', 'text', array(
                    'label' => 'ekyna_core.field.name',
                    'required' => true,
                ));
                $form->add('parent', 'entity', array(
                    'label' => 'ekyna_core.field.parent',
                    'class' => $this->dataClass,
                    'query_builder' => function(EntityRepository $er) use ($page) {
                        $qb = $er->createQueryBuilder('p')
                            ->where('p.locked = :locked')
                            ->orderBy('p.left', 'ASC')
                            ->setParameter('locked', false);
                        if(0 < $page->getId()) {
                            $qb->andWhere('p.id != :id')
                                ->setParameter('id', $page->getId());
                        }
                        return $qb;
                    },
                    'property' => 'name',
                    'required' => true,
                ));
                if(null === $page->getId()) {
                    $form->add('path', 'text', array(
                        'label' => 'ekyna_core.field.url',
                        'required' => false,
                        'attr' => array('input_group' => array('prepend' => $page->getParent()->getPath().'/')),
                    ));
                }else{
                    $form->add('path', 'text', array(
                        'label' => 'ekyna_core.field.url',
                        'disabled' => true,
                    ));
                }
            }
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	return 'ekyna_cms_page';
    }
}
