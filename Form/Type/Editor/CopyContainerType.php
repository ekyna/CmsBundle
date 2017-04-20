<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class CopyContainerType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class CopyContainerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('title')
            ->remove('data')
            ->add('copy', EntityType::class, [
                'label'         => 'Copier',
                'class'         => $options['data_class'],
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('c');

                    return $qb
                        ->andWhere($qb->expr()->isNotNull('c.title'))
                        ->orderBy('c.title', 'ASC');
                },
                'choice_value'  => 'id',
                'choice_label'  => 'title',
                'constraints'   => [
                    new NotNull(),
                ],
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_container_copy';
    }

    public function getParent(): ?string
    {
        return BaseContainerType::class;
    }
}
