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
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'ekyna_core.field.title',
                'admin_helper' => 'PAGE_TITLE',
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page */
            $translation = $event->getData();
            if (null !== $translation && null !== $page = $translation->getTranslatable()) {
                if (!$page->getAdvanced()) {
                    $form = $event->getForm();
                    $form->add('html', 'tinymce', array(
                        'label' => 'ekyna_core.field.content',
                        'theme' => 'advanced',
                    ));
                }
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
