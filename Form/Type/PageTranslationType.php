<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PageTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageTranslationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'ekyna_core.field.title',
                'admin_helper' => 'PAGE_TITLE',
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            $translation = $event->getData();
            if (null === $translation) {
                /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page */
                $page = $form->getParent()->getParent()->getData();
                if (null !== $page && null !== $parent = $page->getParent()) {
                    $form->add('path', 'text', array(
                        'label' => 'ekyna_core.field.url',
                        'admin_helper' => 'PAGE_PATH',
                        'required' => false,
                        'attr' => array('input_group' => array(
                            'prepend' => rtrim($parent->translate($form->getName())->getPath(), '/').'/')
                        ),
                    ));
                } else {
                    $form->add('path', 'text', array(
                        'label' => 'ekyna_core.field.url',
                        'admin_helper' => 'PAGE_PATH',
                        'required' => false,
                    ));
                }
            } else {
                /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page */
                if (null !== $page = $translation->getTranslatable()) {
                    if (!$page->getAdvanced()) {
                        $form->add('html', 'tinymce', array(
                            'label' => 'ekyna_core.field.content',
                            'theme' => 'advanced',
                        ));
                    }
                }
                $form->add('path', 'text', array(
                    'label' => 'ekyna_core.field.url',
                    'disabled' => true,
                ));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Ekyna\Bundle\CmsBundle\Entity\PageTranslation',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_page_translation';
    }
}
