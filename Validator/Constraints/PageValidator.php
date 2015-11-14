<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

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
     * @param array $pageConfig
     * @param array $locales
     */
    public function __construct(array $pageConfig, array $locales)
    {
        $this->pageConfig = $pageConfig;
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($page, Constraint $constraint)
    {
        if (!$constraint instanceof Page) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Page');
        }
        if (!$page instanceof PageInterface) {
            throw new UnexpectedTypeException($page, 'Ekyna\Bundle\CmsBundle\Model\PageInterface');
        }

        /**
         * @var PageInterface $page
         * @var Page          $constraint
         */

        // Validates the translations title
        if (!in_array('Generator', $constraint->groups)) {
            foreach ($this->locales as $locale) {
                if (0 === strlen($page->translate($locale, true)->getTitle())) {
                    $this->context->addViolationAt('translations[' . $locale . '].title', $constraint->titleIsMandatory);
                    return;
                }
            }
        }

        // Validates the controller
        if (!$page->getStatic()) {
            if (null === $controller = $page->getController()) {
                $this->context->addViolationAt('controller', $constraint->controllerIsMandatory);
            }
            if (!array_key_exists($controller, $this->pageConfig['controllers'])) {
                $this->context->addViolationAt('controller', $constraint->invalidController);
            }
        }
    }
}
