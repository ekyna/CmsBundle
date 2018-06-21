<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BaseContainerType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BaseContainerType extends AbstractType
{
    /**
     * @var string
     */
    private $containerClass;


    /**
     * Constructor.
     *
     * @param string $containerClass
     */
    public function __construct(string $containerClass)
    {
        $this->containerClass = $containerClass;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label'    => 'ekyna_core.field.title',
            'required' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->containerClass,
        ]);
    }
}
