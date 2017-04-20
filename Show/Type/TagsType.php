<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Show\Type;

use Ekyna\Bundle\AdminBundle\Show\Type\AbstractType;
use Ekyna\Bundle\AdminBundle\Show\View;
use Ekyna\Bundle\CmsBundle\Model\TagsSubjectInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagsType
 * @package Ekyna\Bundle\CmsBundle\Show\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TagsType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function build(View $view, $value, array $options = []): void
    {
        parent::build($view, $value, $options);

        if ($value instanceof TagsSubjectInterface) {
            $value = $value->getTags()->toArray();
        }

        $view->vars['value'] = $value;
    }

    public static function getName(): string
    {
        return 'tags';
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label'              => 'tag.label.plural',
            'label_trans_domain' => 'EkynaCms',
        ]);
    }
}
