<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Helper\RoutingHelper;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
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
    /**
     * @var RoutingHelper
     */
    private $routingHelper;

    /**
     * @var array
     */
    private $pageConfig;

    /**
     * @var array
     */
    private $locales;


    /**
     * Constructor.
     *
     * @param RoutingHelper $routingHelper
     * @param array         $pageConfig
     * @param array         $locales
     */
    public function __construct(RoutingHelper $routingHelper, array $pageConfig, array $locales)
    {
        $this->routingHelper = $routingHelper;
        $this->pageConfig    = $pageConfig;
        $this->locales       = $locales;
    }

    /**
     * @inheritdoc
     */
    public function validate($page, Constraint $constraint)
    {
        if (!$constraint instanceof Page) {
            throw new UnexpectedTypeException($constraint, Page::class);
        }
        if (!$page instanceof PageInterface) {
            throw new UnexpectedTypeException($page, PageInterface::class);
        }

        /**
         * @var PageInterface $page
         * @var Page          $constraint
         */

        // Validates the translations title
        if (!in_array('Generator', $constraint->groups)) {
            foreach ($this->locales as $locale) {
                if (0 === strlen($page->translate($locale, true)->getTitle())) {
                    $this->context
                        ->buildViolation($constraint->titleIsMandatory)
                        ->atPath('translations[' . $locale . '].title')
                        ->addViolation();

                    return;
                }
            }
        }

        // Validates routing
        if ($page->isStatic()) {
            if (null === $route = $this->routingHelper->findRouteByName($page->getRoute())) {
                $this->context
                    ->buildViolation($constraint->routeNotFound)
                    ->atPath('route')
                    ->addViolation();
            } else {
                /** @var \Ekyna\Bundle\CmsBundle\Model\PageTranslationInterface $translation */
                foreach ($page->getTranslations() as $translation) {
                    $locale = $translation->getLocale();

                    $expected = $this
                        ->routingHelper
                        ->buildPagePath($page->getRoute(), $locale);

                    if ($expected === $translation->getPath()) {
                        continue;
                    }

                    $this->context
                        ->buildViolation($constraint->invalidPath)
                        ->atPath('translations[' . $locale . '].path')
                        ->addViolation();
                }
            }
        } else {
            // Check that the parent page is not locked
            if (!is_null($parentPage = $page->getParent()) && $parentPage->isLocked()) {
                $this->context
                    ->buildViolation($constraint->invalidParent)
                    ->atPath('parent')
                    ->addViolation();
            }
            // Check that the controller is defined
            if (null === $controller = $page->getController()) {
                $this->context
                    ->buildViolation($constraint->controllerIsMandatory)
                    ->atPath('controller')
                    ->addViolation();
            }
            // Check that the controller exists
            if (!array_key_exists($controller, $this->pageConfig['controllers'])) {
                $this->context
                    ->buildViolation($constraint->invalidController)
                    ->atPath('controller')
                    ->addViolation();
            }
        }
    }
}
