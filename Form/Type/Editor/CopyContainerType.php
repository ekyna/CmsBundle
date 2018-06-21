<?php

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
    /**
     * @var string
     */
    private $containerClass;


    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->containerClass = $class;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('copy', EntityType::class, [
            'label' => 'Copier',
            'class' => $this->containerClass,
            'query_builder' => function(EntityRepository $er) {
                $qb = $er->createQueryBuilder('c');
                return $qb
                    ->andWhere($qb->expr()->isNotNull('c.title'))
                    ->orderBy('c.title', 'ASC');
            },
            'choice_value' => 'id',
            'choice_label' => 'title',
            'constraints' => [
                new NotNull(),
            ]
        ]);
    }
}
