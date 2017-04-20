<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Renderer;

use Ekyna\Bundle\CmsBundle\Service\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Service\LocaleSwitcher;
use Ekyna\Component\Resource\Locale\LocaleProviderInterface;
use Twig\Environment;

use function array_replace;
use function trim;

/**
 * Class LocaleSwitcherRenderer
 * @package Ekyna\Bundle\CmsBundle\Service\Renderer
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class LocaleSwitcherRenderer
{
    private LocaleSwitcher          $localeSwitcher;
    private LocaleProviderInterface $localeProvider;
    private PageHelper              $pageHelper;
    private Environment             $twig;
    private string                  $defaultTemplate;

    public function __construct(
        LocaleSwitcher $localeSwitcher,
        LocaleProviderInterface $localeProvider,
        PageHelper $pageHelper,
        Environment $twig,
        string $template = '@EkynaCms/Widget/locale.html.twig'
    ) {
        $this->localeSwitcher = $localeSwitcher;
        $this->localeProvider = $localeProvider;
        $this->pageHelper = $pageHelper;
        $this->twig = $twig;
        $this->defaultTemplate = $template;
    }

    /**
     * Renders the locale switcher.
     */
    public function renderLocaleSwitcher(array $options = []): string
    {
        if (!$this->localeSwitcher->hasResource()) {
            $this->localeSwitcher->setResource($this->pageHelper->getCurrent());
        }

        $options = array_replace([
            'dropdown' => true,
            'tag'      => 'div',
            'attr'     => [],
            'locales'  => [],
            'template' => $this->defaultTemplate,
        ], $options);

        if (empty($urls = $this->localeSwitcher->getUrls($options['locales']))) {
            return '';
        }

        $current = $this->localeProvider->getCurrentLocale();

        $entries = [];
        foreach ($urls as $locale => $url) {
            $entries[$locale] = $url;
        }

        if (!isset($options['attr']['id'])) {
            $options['attr']['id'] = 'locale-switcher';
        }
        if ($options['dropdown']) {
            $classes = $options['attr']['class'] ?? '';
            $classes = trim($classes . ' dropdown');
            $options['attr']['class'] = $classes;
        }

        return $this->twig->render($options['template'], [
            'tag'      => $options['tag'],
            'dropdown' => $options['dropdown'],
            'attr'     => $options['attr'],
            'current'  => $current,
            'locales'  => $entries,
        ]);
    }
}
