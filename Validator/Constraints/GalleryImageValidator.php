<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\GalleryImageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class GalleryImageValidator
 * @package Ekyna\Bundle\CoreBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryImageValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($galleryImage, Constraint $constraint)
    {
        if (!$constraint instanceof GalleryImage) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\GalleryImage');
        }
        if (! $galleryImage instanceof GalleryImageInterface) {
            throw new UnexpectedTypeException($galleryImage, 'Ekyna\Bundle\CmsBundle\Model\GalleryImageInterface');
        }

        /**
         * @var GalleryImage          $constraint
         * @var GalleryImageInterface $galleryImage
         */
        $image = $galleryImage->getImage();
        if (null === $image || (!$image->hasFile() && !$image->hasPath())) {
            $this->context->addViolationAt(
                'image.file',
                $constraint->fileIsMandatory
            );
        }
    }
}
