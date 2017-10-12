<?php

namespace Ekyna\Bundle\CmsBundle\SlideShow\Type;

/**
 * Class AbstractType
 * @package Ekyna\Bundle\CmsBundle\SlideShow\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractType implements TypeInterface
{
    /**
     * Returns the default theme choices.
     *
     * @return array
     */
    static public function getDefaultThemeChoices()
    {
        return [
            'light' => 'Light',
            'dark'  => 'Dark',
        ];
    }

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $jsPath;

    /**
     * @var array
     */
    protected $config;


    /**
     * Constructor.
     *
     * @param string $name
     * @param string $label
     * @param string $jsPath
     * @param array  $config
     */
    public function configure($name, $label, $jsPath, array $config = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->jsPath = $jsPath;
        $this->config = array_replace($this->getConfigDefaults(), $config);
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns the jsPath.
     *
     * @return string
     */
    public function getJsPath()
    {
        return $this->jsPath;
    }

    /**
     * Returns the config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Appends a wrapper to the element.
     *
     * @param \DOMElement $element
     * @param \DOMDocument $dom
     *
     * @return \DOMElement
     */
    public function appendWrapper(\DOMElement $element, \DOMDocument $dom)
    {
        $wrapper = $dom->createElement('div');
        $wrapper->setAttribute('class', 'cms-slide-wrapper');

        $element->appendChild($wrapper);

        return $wrapper;
    }

    /**
     * Returns the config defaults.
     *
     * @return array
     */
    protected function getConfigDefaults()
    {
        return [];
    }

    /**
     * Explodes the style attribute.
     *
     * @param string $style
     *
     * @return array
     */
    protected function explodeStyle($style)
    {
        $data = [];

        $couples = explode(';', $style);

        foreach ($couples as $couple) {
            list($key, $value) = explode(':', $couple);
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Implodes the style attribute.
     *
     * @param array $style
     *
     * @return string
     */
    protected function implodeStyle(array $style)
    {
        $couples = [];

        foreach ($style as $key => $value) {
            $couples[] = "$key:$value";
        }

        return implode(';', $couples);
    }
}
