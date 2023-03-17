<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Page
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Page extends Constraint
{
    public string $invalidParent         = 'ekyna_cms.page.invalid_parent';
    public string $invalidPath           = 'ekyna_cms.page.invalid_path';
    public string $routeNotFound         = 'ekyna_cms.page.route_not_found';
    public string $titleIsMandatory      = 'ekyna_cms.page.title_is_mandatory';
    public string $controllerXorTemplate = 'ekyna_cms.page.controller_xor_template';
    public string $invalidController     = 'ekyna_cms.page.invalid_controller';
    public string $invalidTemplate       = 'ekyna_cms.page.invalid_template';


    /**
     * @inheritDoc
     */
    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
