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
     * @inheritdoc
     *
     * @param Model\Tabs $tabs
     */
    public function normalize($tabs, $format = null, array $context = [])
    {
        $data = [
            'theme'        => $tabs->getTheme(),
            'align'        => $tabs->getAlign(),
            'translations' => [],
            'tabs'         => [],
        ];

        /** @var Model\TabsTranslation $translation */
        foreach ($tabs->getTranslations() as $translation) {
            $data['translations'][$translation->getLocale()] = [
                'locale'       => $translation->getLocale(),
                'title'        => $translation->getTitle(),
                'content'      => $translation->getContent(),
                'media'        => $translation->getMedia() ? $translation->getMedia()->getId() : null,
                'button_label' => $translation->getButtonLabel(),
                'button_url'   => $translation->getButtonUrl(),
            ];
        }

        $list = $tabs->getTabs()->toArray();
        usort($list, function (Model\Tab $a, Model\Tab $b) {
            return $a->getPosition() - $b->getPosition();
        });

        /** @var Model\Tab $tab */
        foreach ($list as $tab) {
            $datum = [
                'position'     => $tab->getPosition(),
                'anchor'       => $tab->getAnchor(),
                'translations' => [],
            ];

            /** @var Model\TabTranslation $translation */
            foreach ($tab->getTranslations() as $translation) {
                $datum['translations'][$translation->getLocale()] = [
                    'locale'       => $translation->getLocale(),
                    'title'        => $translation->getTitle(),
                    'media'        => $translation->getMedia() ? $translation->getMedia()->getId() : null,
                    'button_label' => $translation->getButtonLabel(),
                    'button_url'   => $translation->getButtonUrl(),
                ];
            }

            $data['tabs'][$tab->getPosition()] = $datum;
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $tabs = new Model\Tabs();
        $tabs
            ->setCurrentLocale($this->localeProvider->getCurrentLocale())
            ->setFallbackLocale($this->localeProvider->getFallbackLocale());

        if (empty($data)) {
            return $tabs;
        }

        $tabs
            ->setTheme($data['theme'])
            ->setAlign($data['align']);

        foreach ($data['translations'] as $trans) {
            $translation = new Model\TabsTranslation();
            $translation
                ->setLocale($trans['locale'])
                ->setTitle($trans['title'])
                ->setContent($trans['content'])
                ->setButtonLabel($trans['button_label'])
                ->setButtonUrl($trans['button_url']);

            if (isset($trans['media'])) {
                $translation->setMedia($this->mediaRepository->find($trans['media']));
            }

            $tabs->addTranslation($translation);
        }

        foreach ($data['tabs'] as $datum) {
            $tab = new Model\Tab();
            $tab
                ->setCurrentLocale($this->localeProvider->getCurrentLocale())
                ->setFallbackLocale($this->localeProvider->getFallbackLocale())
                ->setAnchor($datum['anchor'])
                ->setPosition($datum['position']);

            foreach ($datum['translations'] as $trans) {
                $translation = new Model\TabTranslation();
                $translation
                    ->setLocale($trans['locale'])
                    ->setTitle($trans['title'])
                    ->setButtonLabel(isset($trans['button_label']) ? $trans['button_label'] : null) // TODO TMP isset
                    ->setButtonUrl(isset($trans['button_url']) ? $trans['button_url'] : null); // TODO TMP isset

                if (isset($trans['media'])) {
                    $translation->setMedia($this->mediaRepository->find($trans['media']));
                }

                $tab->addTranslation($translation);
            }

            $tabs->addTab($tab);
        }

        return $tabs;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Model\Tabs;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Model\Tabs::class;
    }
}
