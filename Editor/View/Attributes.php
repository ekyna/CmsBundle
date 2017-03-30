<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidArgumentException;

/**
 * Class Attributes
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Attributes implements AttributesInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $classes;

    /**
     * @var array
     */
    private $styles;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $extras;


    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $this->clear();
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->id = null;
        $this->classes = [];
        $this->styles = [];
        $this->data = [];
        $this->extras = [];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function hasData($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * @inheritdoc
     */
    public function setData($key, $value = null)
    {
        if (is_array($key) && null === $value) {
            $this->data = array_replace_recursive($this->data, $key);
        } elseif (is_array($value) && $this->hasData($key) && is_array($current = $this->getData($key))) {
            $this->data[$key] = array_replace_recursive($current, $value);
        } elseif(is_string($key)) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getData($key = null, $default = null)
    {
        if (0 < strlen($key)) {
            return $this->hasData($key) ? $this->data[$key] : $default;
        }

        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function hasExtra($key)
    {
        return isset($this->extras[$key]);
    }

    /**
     * @inheritdoc
     */
    public function setExtra($key, $value)
    {
        if (!(is_string($key) && is_scalar($value))) {
            throw new InvalidArgumentException('Expected extra key and value as strings.');
        }

        if (in_array($key, ['id', 'class', 'style', 'data'])) {
            throw new InvalidArgumentException("Extra key $key is reserved.");
        }

        if (!preg_match('~^[a-zA-Z0-9-]+$~', $key)) {
            throw new InvalidArgumentException('Invalid extra key.');
        }

        $this->extras[$key] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtra($key = null, $default = null)
    {
        if (0 < strlen($key)) {
            return $this->hasExtra($key) ? $this->extras[$key] : $default;
        }

        return $this->extras;
    }

    /**
     * @inheritdoc
     */
    public function addClass($class)
    {
        if (is_array($class)) {
            foreach ($class as $c) {
                $this->addClass($c);
            }
            return $this;
        }

        if (!preg_match('~^[a-zA-Z0-9-_]+$~', $class)) {
            throw new InvalidArgumentException('Invalid css class.');
        }

        if (!in_array($class, $this->classes)) {
            $this->classes[] = $class;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeClass($class)
    {
        $this->classes = array_filter($this->classes, function($c) use ($class) {
            return $c != $class;
        });

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @inheritdoc
     */
    public function addStyle($key, $value)
    {
        if (!(is_string($key) && is_string($value))) {
            throw new InvalidArgumentException('Expected key and value as strings.');
        }

        $this->styles[$key] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeStyle($key)
    {
        if (isset($this->styles[$key])) {
            unset($this->styles[$key]);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        $result = [];

        if (null !== $this->id) {
            $result['id'] = $this->id;
        }
        if (!empty($this->classes)) {
            $result['class'] = implode(' ', $this->classes);
        }
        if (!empty($this->styles)) {
            $result['style'] = implode(';', array_map(function($key, $value) {
                return "$key:$value";
            }, array_keys($this->styles), array_values($this->styles)));
        }
        if (!empty($this->data)) {
            $result['data'] = $this->data;
        }
        if (!empty($this->extras)) {
            foreach ($this->extras as $key => $value) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
