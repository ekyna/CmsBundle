<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\CmsBundle\Model\ChangeFrequencies;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoTranslationInterface;
use Ekyna\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Ekyna\Bundle\ResourceBundle\Form\Type\ConstantChoiceType;
use Ekyna\Component\Resource\Factory\ResourceFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class SeoType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SeoType extends AbstractResourceType
{
    private ResourceFactoryInterface $factory;

    public function __construct(ResourceFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', TranslationsFormsType::class, [
                'form_type' => SeoTranslationType::class,
                'label'     => false,
            ]);

        if ($options['advanced']) {
            $builder
                ->add('changefreq', ConstantChoiceType::class, [
                    'label'        => t('field.changefreq', [], 'EkynaUi'),
                    'admin_helper' => 'CMS_SEO_CHANGEFREQ',
                    'class'        => ChangeFrequencies::class,
                    'required'     => true,
                ])
                ->add('priority', Type\NumberType::class, [
                    'label'        => t('field.priority', [], 'EkynaUi'),
                    'admin_helper' => 'CMS_SEO_PRIORITY',
                    'scale'        => 1,
                    'required'     => true,
                ])
                ->add('follow', Type\CheckboxType::class, [
                    'label'        => t('field.follow', [], 'EkynaUi'),
                    'admin_helper' => 'CMS_SEO_FOLLOW',
                    'required'     => false,
                    'attr'         => ['align_with_widget' => true],
                ])
                ->add('index', Type\CheckboxType::class, [
                    'label'        => t('field.index', [], 'EkynaUi'),
                    'admin_helper' => 'CMS_SEO_INDEX',
                    'required'     => false,
                    'attr'         => ['align_with_widget' => true],
                ])
                ->add('canonical', Type\UrlType::class, [
                    'label'        => t('field.canonical_url', [], 'EkynaUi'),
                    'admin_helper' => 'CMS_SEO_CANONICAL',
                    'required'     => false,
                ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            if (null === $event->getData()) {
                $event->setData($this->factory->create());
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            /** @var SeoInterface $seo */
            $seo = $event->getData();

            if (null !== $seo) {
                $null = true;
                /** @var SeoTranslationInterface[] $translations */
                $translations = $seo->getTranslations();
                if (!empty($translations)) {
                    foreach ($translations as $t) {
                        if (!$t->isEmpty()) {
                            $null = false;
                            break;
                        }
                    }
                }
                if ($null) {
                    $event->setData(null);
                }
            }
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['advanced'] = $options['advanced'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'label'    => false,
                'attr'     => ['widget_col' => 12],
                'advanced' => true,
            ])
            ->setAllowedTypes('advanced', 'bool');
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_seo';
    }
}
