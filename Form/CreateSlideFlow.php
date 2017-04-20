<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Ekyna\Bundle\CmsBundle\Form\Type\SlideType;

/**
 * Class CreateSlideFlow
 * @package Ekyna\Bundle\CmsBundle\Form
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class CreateSlideFlow extends FormFlow
{
    /**
     * @inheritDoc
     */
    protected function loadStepsConfig(): array
    {
        return [
            [
                'label'        => 'type',
                'form_type'    => SlideType::class,
                'form_options' => [
                    'type_mode' => true,
                ],
            ],
            [
                'label'     => 'compose',
                'form_type' => SlideType::class,
            ],
        ];
    }

}
