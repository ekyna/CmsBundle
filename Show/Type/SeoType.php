<?php

namespace Ekyna\Bundle\CmsBundle\Show\Type;

use Ekyna\Bundle\AdminBundle\Show\Exception\InvalidArgumentException;
use Ekyna\Bundle\AdminBundle\Show\Type\AbstractType;
use Ekyna\Bundle\AdminBundle\Show\View;
use Ekyna\Bundle\CmsBundle\Entity\Seo;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SeoType
 * @package Ekyna\Bundle\CmsBundle\Show\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SeoType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function build(View $view, $value, array $options = [])
    {
        if (null === $value) {
            $value = new Seo();
        }

        if (!$value instanceof SeoInterface) {
            throw new InvalidArgumentException("Expected instance of " . SeoInterface::class);
        }

        parent::build($view, $value, $options);

        $view->vars['prefix'] = $options['prefix'] ?? $options['id'] ?: 'seo';
    }

    /**
     * @inheritDoc
     */
    public function getWidgetPrefix()
    {
        return 'seo';
    }

    /**
     * @inheritDoc
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label_col'  => 0,
                'widget_col' => 12,
                'prefix'     => null,
            ])
            ->setAllowedTypes('prefix', ['null', 'string']);
    }
}