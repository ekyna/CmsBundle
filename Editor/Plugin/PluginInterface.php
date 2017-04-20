<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

/**
 * Interface PluginInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface PluginInterface
{
    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Returns the supported block type.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the javascript file path.
     *
     * @return string
     */
    public function getJavascriptFilePath(): string;
}
