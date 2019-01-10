<?php

namespace Ekyna\Bundle\CmsBundle\Service\Serializer;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Component\Resource\Locale\LocaleProviderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class TabsNormalizer
 * @package Ekyna\Bundle\CmsBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabsNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @var MediaRepository
     */
    protected $mediaRepository;


    /**
     * Constructor.
     *
     * @param LocaleProviderInterface $localeProvider
     * @param MediaRepository         $mediaRepository
     */
    public function __construct(
        LocaleProviderInterface $localeProvider,
        MediaRepository $mediaRepository
    ) {
        $this->localeProvider = $localeProvider;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @inheritDoc
     *
     * @param Model\Tabs $tabs
     */
    public function normalize($tabs, $format = null, array $context = [])
    {
        $data = [
            'theme'        => $tabs->getTheme(),
            'align'        => $tabs->getAlign(),
            'media'        => $tabs->getMedia() ? $tabs->getMedia()->getId() : null,
            'translations' => [],
            'tabs'         => [],
        ];

        /** @var Model\TabsTranslation $translation */
        foreach ($tabs->getTranslations() as $translation) {
            $data['translations'][$translation->getLocale()] = [
                'locale'       => $translation->getLocale(),
                'title'        => $translation->getTitle(),
                'content'      => $translation->getContent(),
                'button_label' => $translation->getButtonLabel(),
                'button_url'   => $translation->getButtonUrl(),
            ];
        }

        foreach ($tabs->getTabs() as $tab) {
            $datum = [
                'position'     => $tab->getPosition(),
                'media'        => $tab->getMedia() ? $tab->getMedia()->getId() : null,
                'anchor'       => $tab->getAnchor(),
                'translations' => [],
            ];

            /** @var Model\TabTranslation $translation */
            foreach ($tab->getTranslations() as $translation) {
                $datum['translations'][$translation->getLocale()] = [
                    'locale' => $translation->getLocale(),
                    'title'  => $translation->getTitle(),
                ];
            }

            $data['tabs'][$tab->getPosition()] = $datum;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $tabs = new Model\Tabs();
        $tabs
            ->setTheme($data['theme'])
            ->setAlign($data['align'])
            ->setCurrentLocale($this->localeProvider->getCurrentLocale())
            ->setFallbackLocale($this->localeProvider->getFallbackLocale());

        if (isset($data['media'])) {
            $tabs->setMedia($this->mediaRepository->find($data['media']));
        }

        foreach ($data['translations'] as $trans) {
            $translation = new Model\TabsTranslation();
            $translation
                ->setLocale($trans['locale'])
                ->setTitle($trans['title'])
                ->setContent($trans['content'])
                ->setButtonLabel($trans['button_label'])
                ->setButtonUrl($trans['button_url']);

            $tabs->addTranslation($translation);
        }

        foreach ($data['tabs'] as $datum) {
            $tab = new Model\Tab();
            $tab
                ->setCurrentLocale($this->localeProvider->getCurrentLocale())
                ->setFallbackLocale($this->localeProvider->getFallbackLocale())
                ->setAnchor($datum['anchor'])
                ->setPosition($datum['position']);

            if (isset($datum['media'])) {
                $tab->setMedia($this->mediaRepository->find($datum['media']));
            }

            foreach ($datum['translations'] as $trans) {
                $translation = new Model\TabTranslation();
                $translation
                    ->setLocale($trans['locale'])
                    ->setTitle($trans['title']);

                $tab->addTranslation($translation);
            }

            $tabs->addTab($tab);
        }

        return $tabs;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Model\Tabs;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Model\Tabs::class;
    }
}
