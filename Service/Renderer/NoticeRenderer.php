<?php

namespace Ekyna\Bundle\CmsBundle\Service\Renderer;

use Ekyna\Bundle\CmsBundle\Repository\NoticeRepositoryInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class NoticeRenderer
 * @package Ekyna\Bundle\CmsBundle\Service\Renderer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class NoticeRenderer
{
    /**
     * @var NoticeRepositoryInterface
     */
    private $repository;

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var array
     */
    private $config;


    /**
     * Constructor.
     *
     * @param NoticeRepositoryInterface $repository
     * @param EngineInterface           $engine
     */
    public function __construct(NoticeRepositoryInterface $repository, EngineInterface $engine, array $config)
    {
        $this->repository = $repository;
        $this->engine     = $engine;

        $this->config     = array_replace([
            'template' => '@EkynaCms/Cms/Fragment/notices.html.twig',
        ], $config);
    }

    /**
     * Renders the active notices.
     *
     * @return string
     */
    public function render(): string
    {
        if (empty($notices = $this->repository->findActives())) {
            return '';
        }

        return $this->engine->render($this->config['template'], [
            'notices' => $notices,
        ]);
    }
}
