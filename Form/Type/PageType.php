<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use function Symfony\Component\Translation\t;

/**
 * Class PageType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageType extends AbstractResourceType
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            /** @var PageInterface $page */
            $page = $event->getData();
            $form = $event->getForm();

            if ($page->isStatic()) {
                $form
                    ->add('name', Type\TextType::class, [
                        'label'        => t('field.name', [], 'EkynaUi'),
                        'admin_helper' => 'CMS_PAGE_NAME',
                        'disabled'     => true,
                    ])
                    ->add('parent', EntityType::class, [
                        'label'        => t('field.parent', [], 'EkynaUi'),
                        'admin_helper' => 'CMS_PAGE_PARENT',
                        'class'        => $this->dataClass,
                        'choice_label' => 'name',
                        'placeholder'  => t('page.value.root', [], 'EkynaCms'),
                        'disabled'     => true,
                    ])
                    ->add('enabled', Type\CheckboxType::class, [
                        'label'    => t('field.enabled', [], 'EkynaUi'),
                        'required' => false,
                        'disabled' => true,
                        'attr'     => [
                            'align_with_widget' => true,
                        ],
                    ]);
            } else {
                $form
                    ->add('name', Type\TextType::class, [
                        'label'        => t('field.name', [], 'EkynaUi'),
                        'admin_helper' => 'CMS_PAGE_PATH',
                        'required'     => true,
                    ])
                    ->add('parent', EntityType::class, [
                        'label'         => t('field.parent', [], 'EkynaUi'),
                        'admin_helper'  => 'CMS_PAGE_PARENT',
                        'class'         => $this->dataClass,
                        //'property_path' => 'name',
                        'required'      => true,
                        'query_builder' => function (EntityRepository $er) use ($page): QueryBuilder {
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
                        'label'        => t('field.enabled', [], 'EkynaUi'),
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

                $form
                    ->add('controller', Type\ChoiceType::class, [
                        'label'        => t('page.field.controller', [], 'EkynaCms'),
                        'admin_helper' => 'CMS_PAGE_CONTROLLER',
                        'choices'      => $controllers,
                        'required'     => false,
                    ])
                    ->add('template', Type\TextType::class, [
                        'label'        => t('page.field.template', [], 'EkynaCms'),
                        'admin_helper' => 'CMS_PAGE_TEMPLATE',
                        'required'     => false,
                    ]);
            }
        });
    }
}
