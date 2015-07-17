<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class PageValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageValidator extends ConstraintValidator
{
    /**
     * @var array
     */
    private $locales;


    /**
     * @param array $locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($page, Constraint $constraint)
    {
        if (!$constraint instanceof Page) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Page');
        }
        if (!$page instanceof PageInterface) {
            throw new UnexpectedTypeException($page, 'Ekyna\Bundle\CmsBundle\Model\PageInterface');
        }

        if (0 === $page->getLevel() || $page->getLocked() || $page->getStatic()) {
            return;
        }

        /**
         * @var PageInterface $page
         * @var Page $constraint
         */
        foreach ($this->locales as $locale) {
            if (0 === strlen($page->translate($locale, true)->getTitle())) {
                $this->context->addViolationAt('translations[' . $locale . '].title', $constraint->invalid_title);
                return;
            }
        }
        // TODO unique title by translatable->parent
    }
}
