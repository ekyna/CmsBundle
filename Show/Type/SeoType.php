<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Show\Type;

use Ekyna\Bundle\AdminBundle\Show\Type\AbstractType;
use Ekyna\Bundle\AdminBundle\Show\View;
use Ekyna\Bundle\CmsBundle\Entity\Seo;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Component\Resource\Exception\UnexpectedTypeException;
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
    public function build(View $view, $value, array $options = []): void
    {
        if (null === $value) {
            $value = new Seo();
        }

        if (!$value instanceof SeoInterface) {
            throw new UnexpectedTypeException($value, SeoInterface::class);
        }

        parent::build($view, $value, $options);

        $view->vars['prefix'] = $options['prefix'] ?? $options['id'] ?: 'seo';
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'seo';
    }

    /**
     * @inheritDoc
     */
    protected function configureOptions(OptionsResolver $resolver): void
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
