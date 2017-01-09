<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class Attributes
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Attributes implements AttributesInterface
{
    /**
     * @var array
     */
    private $data;


    /**
     * @inheritDoc
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        if (is_array($value) && $this->has($name) && is_array($current = $this->get($name))) {
            $this->data[$name] = array_replace_recursive($current, $value);
        } else {
            $this->data[$name] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $attributes)
    {
        $this->data = array();
        foreach ($attributes as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $return = null;
        if (array_key_exists($name, $this->data)) {
            $return = $this->data[$name];
            unset($this->data[$name]);
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $return = $this->data;
        $this->data = array();

        return $return;
    }
}
