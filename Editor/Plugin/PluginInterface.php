<?php


namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;


/**
 * Interface PluginInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface PluginInterface
{
    /**
     * Returns the supported block type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the javascript file path.
     *
     * @return string
     */
    public function getJavascriptFilePath();
}
