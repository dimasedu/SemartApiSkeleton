<?php

declare(strict_types=1);

namespace App\Logger;

use App\Util\Serializer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@alpabit.com>
 */
final class DatabaseLogger implements EventSubscriber
{
    private $serializer;

    private $tokenStorage;

    private $logger;

    public function __construct(Serializer $serializer, TokenStorageInterface $tokenStorage, LoggerInterface $logger)
    {
        $this->serializer = $serializer;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->log('CREATE', $args);
    }
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->log('UPDATE', $args);
    }
    public function preRemove(LifecycleEventArgs $args): void
    {
        $this->log('DELETE', $args);
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preRemove,
            Events::preUpdate,
        ];
    }

    private function log(string $action, LifecycleEventArgs $event): void
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return;
        }

        $context = ['groups' => 'read'];
        $object = $event->getObject();
        if ('UPDATE' === $action) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $event->getObjectManager();
            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($object);

            $this->logger->info(sprintf('[%s]<%s>[%s]', $token->getUsername(), $action, json_encode($changeSet)));
        } else {
            $this->logger->info(sprintf('[%s]<%s>[%s]', $token->getUsername(), $action, $this->serializer->serialize($object, 'json', $context)));
        }
    }
}