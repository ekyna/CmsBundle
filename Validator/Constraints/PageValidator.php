<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

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
     * @param UrlGeneratorInterface $urlGenerator
     * @param array                 $pageConfig
     * @param array                 $locales
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, array $pageConfig, array $locales)
    {
        $this->urlGenerator = $urlGenerator;
        $this->pageConfig = $pageConfig;
        $this->locales = $locales;
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

        // Validates the controller
        if ($page->isStatic()) {
            if (!$page->isDynamicPath()) {
                /** @var \Ekyna\Bundle\CmsBundle\Model\PageTranslationInterface $translation */
                foreach ($page->getTranslations() as $translation) {
                    $current = $translation->getPath();
                    $locale = $translation->getLocale();
                    $expected = $this->urlGenerator->generate($page->getRoute(), ['_locale' => $locale]);
                    if (0 === strpos($expected, '/app_dev.php/')) {
                        $expected = substr($expected, strlen('/app_dev.php'));
                    }
                    if (0 === strpos($expected, '/'.$locale.'/')) {
                        $expected = substr($expected, strlen('/'.$locale));
                    }
                    if ($current != $expected) {
                        $this->context
                            ->buildViolation($constraint->invalidPath)
                            ->atPath('translations[' . $locale . '].path')
                            ->addViolation();
                    }
                }
            }
        } else {
            // Check that the parent page is not locked
            if (null !== $parentPage = $page->getParent()) {
                if ($parentPage->isLocked()) {
                    $this->context
                        ->buildViolation($constraint->invalidParent)
                        ->atPath('parent')
                        ->addViolation();
                }
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
