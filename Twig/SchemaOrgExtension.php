<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Service\SchemaOrg\BuilderInterface;

/**
 * Class SchemaOrgExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SchemaOrgExtension extends \Twig_Extension
{
    /**
     * @var BuilderInterface
     */
    protected $builder;


    /**
     * Constructor.
     *
     * @param BuilderInterface $builder
     */
    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('json_ld', [$this, 'build'], ['is_safe' => ['html']]),
        ];
    }

    public function build($object)
    {
        if (null !== $schema = $this->builder->build($object)) {
            return $schema->toScript();
        }

        return '';
    }
}
