<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\CmsBundle\Entity\PageTranslation;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\UiBundle\Form\Type\TinymceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class PageTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'        => t('field.title', [], 'EkynaUi'),
                'admin_helper' => 'CMS_PAGE_TITLE',
            ])
            ->add('breadcrumb', TextType::class, [
                'label'        => t('field.breadcrumb', [], 'EkynaUi'),
                'admin_helper' => 'CMS_PAGE_BREADCRUMB',
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();

            /** @var PageInterface $page */
            $page = $form->getParent()->getParent()->getData();
            $parent = $page->getParent();
            if ($page && !$page->isStatic() && $parent) {
                $pathOptions = [
                    'label'        => t('field.url', [], 'EkynaUi'),
                    'admin_helper' => 'CMS_PAGE_PATH',
                    'required'     => false,
                    'disabled'     => $page->isStatic(),
                ];
                $parentPath = $parent->translate($form->getName())->getPath();
                if (1 < strlen($parentPath)) {
                    if (16 < strlen($parentPath)) {
                        $parentPath = '&hellip;' . substr($parentPath, -16);
                    }
                    $pathOptions['attr'] = [
                        'input_group' => [
                            'prepend' => rtrim($parentPath, '/') . '/',
                        ],
                    ];
                }
                $form->add('path', TextType::class, $pathOptions);
            } else {
                $form->add('path', TextType::class, [
                    'label'        => t('field.url', [], 'EkynaUi'),
                    'admin_helper' => 'CMS_PAGE_PATH',
                    'required'     => false,
                    'disabled'     => ($page && $page->isStatic()),
                ]);
            }

            if (!$page->isAdvanced()) {
                $form->add('html', TinymceType::class, [
                    'label'        => t('field.content', [], 'EkynaUi'),
                    'admin_helper' => 'CMS_PAGE_CONTENT',
                    'theme'        => 'advanced',
                    'required'     => false,
                ]);
            }
        });

        $builder->addModelTransformer(new CallbackTransformer(
        // Transform
            function ($data) {
                if (null === $data) {
                    return null;
                }

                /**
                 * @var PageTranslation $data
                 * @var PageInterface   $page
                 */
                if (!empty($path = $data->getPath())) {
                    $page = $data->getTranslatable();
                    // Path field is disabled for static pages : skip transform.
                    if ($page->isStatic()) {
                        return $data;
                    }
                    if (null !== $parent = $page->getParent()) {
                        $parentPath = $parent->translate($data->getLocale())->getPath();
                        $path = substr($path, strlen(rtrim($parentPath, '/')));
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', PageTranslation::class);
    }
}
