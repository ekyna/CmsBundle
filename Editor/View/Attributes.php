<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\View;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidArgumentException;

/**
 * Class Attributes
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Attributes implements AttributesInterface
{
    private ?string $id = null;
    private array   $classes;
    private array   $styles;
    private array   $data;
    private array   $extras;


    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $this->clear();
    }

    /**
     * @inheritDoc
     */
    public function clear(): Attributes
    {
        $this->id = null;
        $this->classes = [];
        $this->styles = [];
        $this->data = [];
        $this->extras = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setId(string $id = null): AttributesInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function hasData(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * @inheritDoc
     */
    public function setData($key, $value = null): AttributesInterface
    {
        if (is_array($key) && null === $value) {
            $this->data = array_replace_recursive($this->data, $key);
        } elseif (is_array($value) && $this->hasData($key) && is_array($current = $this->getData($key))) {
            $this->data[$key] = array_replace_recursive($current, $value);
        } elseif (is_string($key)) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getData(string $key = null, $default = null)
    {
        if (!empty($key)) {
            return $this->hasData($key) ? $this->data[$key] : $default;
        }

        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function hasExtra(string $key): bool
    {
        return isset($this->extras[$key]);
    }

    /**
     * @inheritDoc
     */
    public function setExtra(string $key, string $value): AttributesInterface
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
     * @inheritDoc
     */
    public function getExtra(string $key = null, string $default = null)
    {
        if (!empty($key)) {
            return $this->hasExtra($key) ? $this->extras[$key] : $default;
        }

        return $this->extras;
    }

    /**
     * @inheritDoc
     */
    public function addClass($class): AttributesInterface
    {
        if (!is_array($class)) {
            $class = $this->parseClass($class);
        }

        if (is_array($class)) {
            foreach ($class as $c) {
                $this->addClass($c);
            }

            return $this;
        }

        if (!preg_match('~^[a-zA-Z0-9-_]+$~', $class)) {
            throw new InvalidArgumentException("CSS class '$class' is not valid.");
        }

        if (!in_array($class, $this->classes)) {
            $this->classes[] = $class;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeClass($class): AttributesInterface
    {
        if (!is_array($class)) {
            $class = $this->parseClass($class);
        }

        if (is_array($class)) {
            foreach ($class as $c) {
                $this->removeClass($c);
            }

            return $this;
        }

        $this->classes = array_filter($this->classes, function ($c) use ($class) {
            return $c != $class;
        });

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @inheritDoc
     */
    public function addStyle(string $key, string $value): AttributesInterface
    {
        if (!(is_string($key) && is_string($value))) {
            throw new InvalidArgumentException('Expected key and value as strings.');
        }

        $this->styles[$key] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeStyle(string $key): AttributesInterface
    {
        if (isset($this->styles[$key])) {
            unset($this->styles[$key]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $result = [];

        if (null !== $this->id) {
            $result['id'] = $this->id;
        }
        if (!empty($this->classes)) {
            $result['class'] = implode(' ', $this->classes);
        }
        if (!empty($this->styles)) {
            $result['style'] = implode(';', array_map(function ($key, $value) {
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

    /**
     * Parses the given class(es).
     *
     * @param string $class
     *
     * @return array|string
     */
    private function parseClass(string $class)
    {
        $class = trim($class);

        if (false !== strpos($class, ' ')) {
            $class = explode(' ', $class);
        }

        return $class;
    }
}
