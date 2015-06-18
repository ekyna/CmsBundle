<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

/**
 * Class AsseticConfiguration
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AsseticConfiguration
{
    /**
     * Builds the assetic configuration.
     *
     * @param array $config
     * @return array
     */
    public function build(array $config)
    {
        $output = array();

        // Fix path in output dir
        if ('/' !== substr($config['output_dir'], -1) && strlen($config['output_dir']) > 0) {
            $config['output_dir'] .= '/';
        }

        $output['cms_editor_css'] = $this->buildCss($config);

        return $output;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function buildCss(array $config)
    {
        $inputs = array(
            '@EkynaCmsBundle/Resources/asset/css/editor.css',
        );

        return array(
            'inputs'  => $inputs,
            'filters' => array('yui_css'),
            'output'  => $config['output_dir'].'css/cms-editor.css',
            'debug'   => false,
        );
    }
}
