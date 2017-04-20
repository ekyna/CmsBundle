<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model\Tab;
use Ekyna\Bundle\UiBundle\Form\Type\CollectionPositionType;
use Ekyna\Component\Resource\Locale\LocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TabsBlockTabType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabType extends AbstractType
{
    private LocaleProviderInterface $localeProvider;

    public function __construct(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', TranslationsFormsType::class, [
                'form_type'      => TabTranslationType::class,
                'label'          => false,
                'error_bubbling' => false,
            ])
            ->add('anchor', TextType::class, [
                'label'    => 'Anchor', // TODO
                'required' => false,
            ])
            ->add('position', CollectionPositionType::class)
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!$data instanceof Tab) {
                    return;
                }

                $data
                    ->setCurrentLocale($this->localeProvider->getCurrentLocale())
                    ->setFallbackLocale($this->localeProvider->getFallbackLocale());
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Tab::class);
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_block_tab';
    }
}
