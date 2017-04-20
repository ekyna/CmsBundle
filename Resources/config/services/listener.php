<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\EventListener\KernelEventListener;
use Ekyna\Bundle\CmsBundle\EventListener\MenuEventListener;
use Ekyna\Bundle\CmsBundle\EventListener\NoticeEventListener;
use Ekyna\Bundle\CmsBundle\EventListener\PageEventListener;
use Ekyna\Bundle\CmsBundle\EventListener\PublicUrlEventSubscriber;
use Ekyna\Bundle\CmsBundle\Listener\ContentSubjectSubscriber;
use Ekyna\Bundle\CmsBundle\Listener\PageElasticaSubscriber;
use Ekyna\Bundle\CmsBundle\Listener\PageTranslationListener;
use Ekyna\Bundle\CmsBundle\Listener\SeoSubjectSubscriber;
use Ekyna\Bundle\CmsBundle\Listener\TagsSubjectSubscriber;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Kernel event listener
        ->set('ekyna_cms.listener.kernel', KernelEventListener::class)
            ->args([
                service('ekyna_cms.editor.editor'),
                service('ekyna_cms.helper.page'),
                service('security.authorization_checker'),
                service('request_stack'),
            ])
            ->tag('kernel.event_subscriber')

        // Page event listener
        ->set('ekyna_cms.listener.page', PageEventListener::class)
            ->args([
                service('ekyna_resource.orm.persistence_helper'),
                service('ekyna_cms.updater.page'),
                service('ekyna_cms.updater.page_redirection'),
            ])
            ->tag('resource.event_subscriber')

        // Menu event listener
        ->set('ekyna_cms.listener.menu', MenuEventListener::class)
            ->args([
                service('ekyna_resource.orm.persistence_helper'),
                service('ekyna_cms.updater.menu'),
            ])
            ->tag('resource.event_subscriber')

        // Notice event listener
        ->set('ekyna_cms.listener.notice', NoticeEventListener::class)
            ->args([
                service('doctrine.orm.entity_manager'),
            ])
            ->tag('resource.event_subscriber')

        // Public URL event listener
        ->set('ekyna_cms.listener.public_url', PublicUrlEventSubscriber::class)
            ->tag('resource.event_subscriber')

        // Metadata listeners
        // TODO merge metadata subscribers
        ->set('ekyna_cms.listener.content_subject_metadata', ContentSubjectSubscriber::class)
            ->tag('doctrine.event_listener', [
                'event'      => 'loadClassMetadata',
                'connection' => 'default',
            ])
        ->set('ekyna_cms.listener.seo_subject_metadata', SeoSubjectSubscriber::class)
            ->tag('doctrine.event_listener', [
                'event'      => 'loadClassMetadata',
                'connection' => 'default',
            ])
        ->set('ekyna_cms.listener.tags_subject_metadata', TagsSubjectSubscriber::class)
            ->tag('doctrine.event_listener', [
                'event'      => 'loadClassMetadata',
                'connection' => 'default',
            ])

        // Page elastica listener
        ->set('ekyna_cms.listener.page_elastica', PageElasticaSubscriber::class)
            ->args([
                service('fos_elastica.object_persister.ekyna_cms.page'),
                param('ekyna_cms.class.page'),
            ])
            ->tag('doctrine.event_listener', [
                'event'      => 'onFlush',
                'connection' => 'default',
            ])

        // Page translation listener
        ->set('ekyna_cms.listener.page_translation', PageTranslationListener::class)
            ->args([
                service('event_dispatcher'),
            ])
            ->tag('doctrine.orm.entity_listener', [
                // TODO  'entity' => 'PageTranslation',
                'lazy' => true
            ])
            ->tag('doctrine.event_listener', [
                'event'      => 'onPostFlush',
                'connection' => 'default',
            ])
    ;
};
