<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\CmsBundle\Entity\NoticeTranslation;
use Ekyna\Bundle\CoreBundle\Form\Type\TinymceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NoticeTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class NoticeTranslationType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', TinymceType::class, [
            'label'        => 'ekyna_core.field.content',
            //'admin_helper' => 'CMS_NOTICE_CONTENT',
            'theme'        => 'advanced',
            'required'     => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => NoticeTranslation::class,
            ]);
    }
}
