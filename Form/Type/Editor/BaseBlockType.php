<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BaseBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BaseBlockType extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create('data', null, [
                'label'    => false,
                'compound' => true,
            ])
        );
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', $this->dataClass);
    }
}
