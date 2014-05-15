<?php

namespace Ekyna\Bundle\CmsBundle\Settings;

use Ekyna\Bundle\SettingBundle\Schema\AbstractSchema;
use Ekyna\Bundle\SettingBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Locale;

/**
 * CmsSettingsSchema.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsSettingsSchema extends AbstractSchema
{
    /**
     * @var array
     */
    protected $defaults;

    /**
     * @param array $defaults
     */
    public function __construct(array $defaults = array())
    {
        $this->defaults = $defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array_merge(array(
                'title'            => 'Page default title',
                'meta_description' => 'Page default description',
                'locale'           => 'fr',
            ), $this->defaults))
            ->setAllowedTypes(array(
                'title'            => array('string'),
                'meta_description' => array('string'),
                'locale'           => array('string'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label'       => 'ekyna_core.field.title',
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('meta_description', 'textarea', array(
                'label'       => 'ekyna_core.field.description',
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('locale', 'locale', array(
                'label'       => 'ekyna_core.field.locale',
                'constraints' => array(
                    new NotBlank(),
                    new Locale(),
                )
            ))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'ekyna_core.field.seo';
    }

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate()
    {
        return 'EkynaCmsBundle:Settings:show.html.twig';
    }

    /**
     * {@inheritDoc}
     */
    public function getFormTemplate()
    {
        return 'EkynaCmsBundle:Settings:form.html.twig';
    }

    public function getName()
    {
        return 'ekyna_cms_settings';
    }
}
