<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Doctrine\ORM\EntityRepository;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class PageType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
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
            ->add('seo', SeoType::class)
            ->add('translations', TranslationsFormsType::class, [
                'form_type'      => PageTranslationType::class,
                'label'          => false,
                'error_bubbling' => false,
                'attr'           => [
                    'widget_col' => 12,
                ],
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page */
            $page = $event->getData();
            $form = $event->getForm();

            if ($page->isStatic()) {
                $form
                    ->add('name', Type\TextType::class, [
                        'label'        => 'ekyna_core.field.name',
                        'admin_helper' => 'CMS_PAGE_NAME',
                        'disabled'     => true,
                    ])
                    ->add('parent', EntityType::class, [
                        'label'        => 'ekyna_core.field.parent',
                        'admin_helper' => 'CMS_PAGE_PARENT',
                        'class'        => $this->dataClass,
                        'choice_label' => 'name',
                        'placeholder'  => 'ekyna_cms.page.value.root',
                        'disabled'     => true,
                    ])
                    ->add('enabled', Type\CheckboxType::class, [
                        'label'    => 'ekyna_core.field.enabled',
                        'required' => false,
                        'disabled' => true,
                        'attr'     => [
                            'align_with_widget' => true,
                        ],
                    ]);
            } else {
                $form
                    ->add('name', Type\TextType::class, [
                        'label'        => 'ekyna_core.field.name',
                        'admin_helper' => 'CMS_PAGE_PATH',
                        'required'     => true,
                    ])
                    ->add('parent', EntityType::class, [
                        'label'         => 'ekyna_core.field.parent',
                        'admin_helper'  => 'CMS_PAGE_PARENT',
                        'class'         => $this->dataClass,
                        //'property_path' => 'name',
                        'required'      => true,
                        'query_builder' => function (EntityRepository $er) use ($page) {
                            $qb = $er
                                ->createQueryBuilder('p')
                                ->where('p.locked = :locked')
                                ->orderBy('p.left', 'ASC')
                                ->setParameter('locked', false);
                            if (0 < $page->getId()) {
                                $qb
                                    ->andWhere('p.id != :id')
                                    ->setParameter('id', $page->getId());
                            }

                            return $qb;
                        },
                    ])
                    ->add('enabled', Type\CheckboxType::class, [
                        'label'        => 'ekyna_core.field.enabled',
                        'admin_helper' => 'CMS_PAGE_ENABLE',
                        'required'     => false,
                        'attr'         => [
                            'align_with_widget' => true,
                        ],
                    ]);

                $controllers = [];
                foreach ($this->config['controllers'] as $name => $config) {
                    $controllers[$config['title']] = $name;
                }

                $form->add('controller', Type\ChoiceType::class, [
                    'label'        => 'ekyna_cms.page.field.controller',
                    'admin_helper' => 'CMS_PAGE_CONTROLLER',
                    'choices'      => $controllers,
                    'required'     => true,
                ]);
            }
        });
    }
}
