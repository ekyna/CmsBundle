<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Behat\Transliterator\Transliterator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'label'        => 'ekyna_core.field.title',
                'admin_helper' => 'CMS_PAGE_TITLE',
            ))
            ->add('breadcrumb', 'text', array(
                'label'        => 'ekyna_core.field.breadcrumb',
                'admin_helper' => 'CMS_PAGE_BREADCRUMB',
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page */
            $page = $form->getParent()->getParent()->getData();
            if (null !== $page && null !== $parent = $page->getParent()) {
                $parentPath = $parent->translate($form->getName())->getPath();
                if (16 < strlen($parentPath)) {
                    $parentPath = '&hellip;'.substr($parentPath, -16);
                }
                $form->add('path', 'text', array(
                    'label'        => 'ekyna_core.field.url',
                    'admin_helper' => 'CMS_PAGE_PATH',
                    'required'     => false,
                    'disabled'     => $page->getStatic(),
                    'attr'         => array('input_group' => array(
                        'prepend' => rtrim($parentPath, '/') . '/')
                    ),
                ));
            } else {
                $form->add('path', 'text', array(
                    'label'        => 'ekyna_core.field.url',
                    'admin_helper' => 'CMS_PAGE_PATH',
                    'required'     => false,
                    'disabled'     => (null !== $page && $page->getStatic()),
                ));
            }

            if (!$page->getAdvanced()) {
                $form->add('html', 'tinymce', array(
                    'label'        => 'ekyna_core.field.content',
                    'admin_helper' => 'CMS_PAGE_CONTENT',
                    'theme'        => 'advanced',
                ));
            }
        });

        $builder->addModelTransformer(new CallbackTransformer(
            // Transform
            function ($data) {
                if (null === $data) {
                    return $data;
                }

                /**
                 * @var \Ekyna\Bundle\CmsBundle\Entity\PageTranslation $data
                 * @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page
                 */
                if (0 < strlen($path = $data->getPath())) {
                    $page = $data->getTranslatable();
                    if (null !== $parent = $page->getParent()) {
                        $parentPath = $parent->translate($data->getLocale())->getPath();
                        $path = substr($path, strlen($parentPath));
                    }
                    $data->setPath(trim($path, '/'));
                }

                return $data;
            },
            // Reverse transform
            function ($data) {
                return $data;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
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
