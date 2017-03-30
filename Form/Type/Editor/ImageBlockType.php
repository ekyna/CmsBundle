<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ImageBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ImageBlockType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $imagesOptions = [
            'label'      => false,
            'repository' => $options['repository'],
            'themes'     => $options['themes'],
            'styles'     => $options['styles'],
            'animations' => $options['animations'],
        ];

        $builder
            ->add('url', Type\UrlType::class, [
                'label'       => 'ekyna_core.field.url',
                'required'    => false,
                'constraints' => [
                    new Assert\Url(),
                ],
            ])
            ->add('image', ImageType::class, $imagesOptions);

        if ($options['with_hover']) {
            $builder->add('hover', ImageType::class, $imagesOptions);
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('repository')
            ->setDefault('themes', [])
            ->setDefault('styles', [])
            ->setDefault('animations', [])
            ->setDefault('with_hover', true)
            ->setAllowedTypes('repository', MediaRepository::class)
            ->setAllowedTypes('themes', 'array')
            ->setAllowedTypes('styles', 'array')
            ->setAllowedTypes('animations', 'array')
            ->setAllowedTypes('with_hover', 'bool');
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_block_image';
    }
}
