<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Component\Resource\Model\TranslationInterface;

/**
 * Interface PageTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method PageInterface getTranslatable()
 */
interface PageTranslationInterface extends TranslationInterface
{
    public function setTitle(?string $title): PageTranslationInterface;

    public function getTitle(): ?string;

    public function setBreadcrumb(?string $breadcrumb): PageTranslationInterface;

    public function getBreadcrumb(): ?string;

    public function setHtml(?string $html): PageTranslationInterface;

    public function getHtml(): ?string;

    public function setPath(?string $path): PageTranslationInterface;

    public function getPath(): ?string;
}
