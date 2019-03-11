<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BaseBlockTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BaseBlockTranslationType extends AbstractType
{
    /**
     * @var string
     */
    private $dataClass;


    /**
     * Constructor.
     *
     * @param string $dataClass
     */
    public function __construct(string $dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', $this->dataClass);
    }
}
