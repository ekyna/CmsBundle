<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Service\Helper\RoutingHelper;
use Ekyna\Bundle\UiBundle\Service\TwigHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class PageValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageValidator extends ConstraintValidator
{
    public function __construct(
        private readonly RoutingHelper $routingHelper,
        private readonly TwigHelper    $twigHelper,
        private readonly array         $controllers,
        private readonly array         $locales
    ) {
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Page) {
            throw new UnexpectedTypeException($constraint, Page::class);
        }
        if (!$value instanceof PageInterface) {
            throw new UnexpectedTypeException($value, PageInterface::class);
        }

        /**
         * @var PageInterface $value
         * @var Page          $constraint
         */

        // Validates the translations title
        if (!in_array('Generator', $constraint->groups)) {
            foreach ($this->locales as $locale) {
                if (empty($value->translate($locale, true)->getTitle())) {
                    $this->context
                        ->buildViolation($constraint->titleIsMandatory)
                        ->atPath('translations[' . $locale . '].title')
                        ->addViolation();

                    return;
                }
            }
        }

        // Validates routing
        if ($value->isStatic()) {
            if (null === $this->routingHelper->findRouteByName($value->getRoute(), null)) {
                $this->context
                    ->buildViolation($constraint->routeNotFound)
                    ->atPath('route')
                    ->addViolation();
            } else {
                foreach ($value->getTranslations() as $translation) {
                    $locale = $translation->getLocale();

                    $expected = $this
                        ->routingHelper
                        ->buildPagePath($value->getRoute(), $locale);

                    if ($expected === $translation->getPath()) {
                        continue;
                    }

                    $this->context
                        ->buildViolation($constraint->invalidPath)
                        ->atPath('translations[' . $locale . '].path')
                        ->addViolation();
                }
            }

            return;
        }

        // Check that the parent page is not locked
        if (!is_null($parentPage = $value->getParent()) && $parentPage->isLocked()) {
            $this->context
                ->buildViolation($constraint->invalidParent)
                ->atPath('parent')
                ->addViolation();

            return;
        }

        $templateIsSet = null !== $template = $value->getTemplate();
        $controllerIsSet = null !== $controller = $value->getController();

        if (!($controllerIsSet xor $templateIsSet)) {
            $this->context
                ->buildViolation($constraint->controllerXorTemplate)
                ->atPath('controller')
                ->addViolation();

            return;
        }

        if ($templateIsSet) {
            if (!$this->twigHelper->templateExists($template)) {
                $this->context
                    ->buildViolation($constraint->invalidTemplate)
                    ->atPath('template')
                    ->addViolation();
            }

            return;
        }

        // Check that the controller exists
        if (!array_key_exists($controller, $this->controllers)) {
            $this->context
                ->buildViolation($constraint->invalidController)
                ->atPath('controller')
                ->addViolation();
        }
    }
}
