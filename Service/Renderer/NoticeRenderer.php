<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Renderer;

use Ekyna\Bundle\CmsBundle\Repository\NoticeRepositoryInterface;
use Twig\Environment;

/**
 * Class NoticeRenderer
 * @package Ekyna\Bundle\CmsBundle\Service\Renderer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class NoticeRenderer
{
    private NoticeRepositoryInterface $repository;
    private Environment               $twig;
    private array                     $config;

    public function __construct(NoticeRepositoryInterface $repository, Environment $twig, array $config)
    {
        $this->repository = $repository;
        $this->twig = $twig;
        $this->config = array_replace([
            'template' => '@EkynaCms/Cms/Fragment/notices.html.twig',
        ], $config);
    }

    /**
     * Renders the active notices.
     */
    public function render(): string
    {
        if (empty($notices = $this->repository->findActives())) {
            return '';
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->twig->render($this->config['template'], [
            'notices' => $notices,
        ]);
    }
}
