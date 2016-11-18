<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Ekyna\Bundle\CmsBundle\Model\ChangeFrequencies;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SeoType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SeoType extends ResourceFormType
{
    /**
     * @var ResourceRepositoryInterface
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param ResourceRepositoryInterface $repository
     * @param string                      $seoClass
     */
    public function __construct(ResourceRepositoryInterface $repository, $seoClass)
    {
        parent::__construct($seoClass);

        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', TranslationsFormsType::class, [
                'form_type' => SeoTranslationType::class,
                'label'     => false,
            ]);

        if ($options['advanced']) {
            $builder
                ->add('changefreq', Type\ChoiceType::class, [
                    'label'        => 'ekyna_core.field.changefreq',
                    'admin_helper' => 'CMS_SEO_CHANGEFREQ',
                    'choices'      => ChangeFrequencies::getChoices(),
                    'required'     => true,
                ])
                ->add('priority', Type\NumberType::class, [
                    'label'        => 'ekyna_core.field.priority',
                    'admin_helper' => 'CMS_SEO_PRIORITY',
                    'scale'        => 1,
                    'required'     => true,
                ])
                ->add('follow', Type\CheckboxType::class, [
                    'label'        => 'ekyna_core.field.follow',
                    'admin_helper' => 'CMS_SEO_FOLLOW',
                    'required'     => false,
                    'attr'         => ['align_with_widget' => true],
                ])
                ->add('index', Type\CheckboxType::class, [
                    'label'        => 'ekyna_core.field.index',
                    'admin_helper' => 'CMS_SEO_INDEX',
                    'required'     => false,
                    'attr'         => ['align_with_widget' => true],
                ])
                ->add('canonical', Type\UrlType::class, [
                    'admin_helper' => 'CMS_SEO_CANONICAL',
                    'label'        => 'ekyna_core.field.canonical_url',
                    'required'     => false,
                ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            if (null === $event->getData()) {
                $event->setData($this->repository->createNew());
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var \Ekyna\Bundle\CmsBundle\Model\SeoInterface $seo */
            $seo = $event->getData();

            if (null !== $seo) {
                $null = true;
                /** @var \Ekyna\Bundle\CmsBundle\Model\SeoTranslationInterface[] $translations */
                $translations = $seo->getTranslations();
                if (!empty($translations)) {
                    foreach ($translations as $t) {
                        if (!$t->isEmpty()) {
                            $null = true;
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

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['advanced'] = $options['advanced'];
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'label'             => false,
                'attr'              => ['widget_col' => 12],
                'advanced'          => true,
                'validation_groups' => [$this->dataClass],
                /*'empty_data'        => function () {
                    return $this->repository->createNew();
                },*/
            ])
            ->setAllowedTypes('advanced', 'bool');

    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_seo';
    }
}
