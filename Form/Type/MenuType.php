<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ekyna\Bundle\CmsBundle\Entity\Menu;
use Ekyna\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Ekyna\Bundle\UiBundle\Form\Type\KeyValueCollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function Symfony\Component\Translation\t;

/**
 * Class MenuType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuType extends AbstractResourceType
{
    protected AuthorizationCheckerInterface $authorization;
    protected string                        $pageClass;

    public function __construct(AuthorizationCheckerInterface $authorization, string $pageClass)
    {
        $this->authorization = $authorization;
        $this->pageClass = $pageClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', Type\TextareaType::class, [
                'label'        => t('field.description', [], 'EkynaUi'),
                'admin_helper' => 'CMS_MENU_DESCRIPTION',
                'required'     => false,
            ]);

        if ($this->authorization->isGranted('ROLE_SUPER_ADMIN')) {
            $builder->add('attributes', KeyValueCollectionType::class, [
                'label'           => t('field.attributes', [], 'EkynaUi'),
                'admin_helper'    => 'CMS_MENU_ATTRIBUTES',
                'add_button_text' => t('button.add_attribute', [], 'EkynaUi'),
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            /** @var Menu $menu */
            $menu = $event->getData();
            $form = $event->getForm();
            $locked = $menu->isLocked();

            $form
                ->add('name', Type\TextType::class, [
                    'label'        => t('field.name', [], 'EkynaUi'),
                    'admin_helper' => 'CMS_MENU_NAME',
                    'required'     => true,
                    'disabled'     => $locked,
                ])
                ->add('parent', EntityType::class, [
                    'label'         => t('field.parent', [], 'EkynaUi'),
                    'admin_helper'  => 'CMS_MENU_PARENT',
                    'choice_label'  => 'title',
                    'required'      => true,
                    'disabled'      => $locked,
                    'class'         => $this->dataClass,
                    'query_builder' => function (EntityRepository $er) use ($menu): QueryBuilder {
                        $qb = $er->createQueryBuilder('m');
                        $qb->addOrderBy('m.left', 'ASC');
                        if (null !== $parent = $menu->getParent()) {
                            $qb
                                ->andWhere('m.root = :root')
                                ->setParameter('root', $parent->getRoot());
                        }
                        if (0 < $menu->getId()) {
                            $qb
                                ->andWhere('m.id != :id')
                                ->setParameter('id', $menu->getId());
                        }

                        return $qb;
                    },
                ])
                ->add('enabled', Type\CheckboxType::class, [
                    'label'        => t('field.enabled', [], 'EkynaUi'),
                    'admin_helper' => 'CMS_MENU_ENABLED',
                    'required'     => false,
                    'disabled'     => $locked,
                    'attr'         => [
                        'align_with_widget' => true,
                    ],
                ])
                ->add('translations', TranslationsFormsType::class, [
                    'form_type'    => MenuTranslationType::class,
                    'form_options' => [
                        'locked' => $locked,
                    ],
                    'label'        => false,
                    'attr'         => [
                        'widget_col' => 12,
                    ],
                ]);

            if ($this->authorization->isGranted('ROLE_SUPER_ADMIN')) {
                $form
                    ->add('route', Type\TextType::class, [
                        'label'        => t('field.route', [], 'EkynaUi'),
                        'admin_helper' => 'CMS_MENU_ROUTE',
                        'required'     => false,
                        'disabled'     => $locked,
                    ])
                    ->add('parameters', KeyValueCollectionType::class, [
                        'label'           => t('field.parameters', [], 'EkynaUi'),
                        'admin_helper'    => 'CMS_MENU_PARAMETERS',
                        'add_button_text' => t('button.add_parameter', [], 'EkynaUi'),
                    ]);
            }

            if (!$locked) {
                $form
                    ->add('page', EntityType::class, [
                        'label'         => t('page.label.singular', [], 'EkynaCms'),
                        'admin_helper'  => 'CMS_MENU_PAGE',
                        'choice_label'  => 'name',
                        'placeholder'   => t('value.choose', [], 'EkynaUi'),
                        'required'      => false,
                        'class'         => $this->pageClass,
                        'query_builder' => function (EntityRepository $er): QueryBuilder {
                            $qb = $er->createQueryBuilder('p');
                            $qb
                                ->andWhere($qb->expr()->eq('p.dynamicPath', ':dynamic'))
                                ->setParameter('dynamic', false)
                                ->addOrderBy('p.left', 'ASC');

                            return $qb;
                        },
                    ]);
            }
        });
    }
}
