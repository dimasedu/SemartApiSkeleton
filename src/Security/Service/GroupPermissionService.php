<?php

declare(strict_types=1);

namespace KejawenLab\Semart\ApiSkeleton\Security\Service;

use KejawenLab\Semart\ApiSkeleton\Security\Model\GroupInterface;
use KejawenLab\Semart\ApiSkeleton\Security\Model\MenuRepositoryInterface;
use KejawenLab\Semart\ApiSkeleton\Security\Model\PermissionableInterface;
use KejawenLab\Semart\ApiSkeleton\Security\Model\PermissionInitiatorInterface;
use KejawenLab\Semart\ApiSkeleton\Security\Model\PermissionRemoverInterface;
use KejawenLab\Semart\ApiSkeleton\Security\Model\PermissionRepositoryInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@alpabit.com>
 */
final class GroupPermissionService implements PermissionInitiatorInterface, PermissionRemoverInterface
{
    private $menuRepository;

    private $permissionRepository;

    private $class;

    public function __construct(MenuRepositoryInterface $menuRepository, PermissionRepositoryInterface $permissionRepository)
    {
        $this->menuRepository = $menuRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function initiate(PermissionableInterface $object): void
    {
        /** @var GroupInterface $object */
        foreach ($this->menuRepository->findAll() as $menu) {
            $permission = $this->permissionRepository->findPermission($object, $menu);
            if (!$permission) {
                $permission = new $this->class();
            }

            $permission->setMenu($menu);
            $permission->setGroup($object);

            $this->permissionRepository->persist($permission);
        }
    }

    public function remove(PermissionableInterface $object): void
    {
        /** @var GroupInterface $object */
        $this->permissionRepository->removeByGroup($object);
    }

    public function support(PermissionableInterface $object): bool
    {
        return $object instanceof GroupInterface;
    }

    public function setClass(string $class): void
    {
        $this->class = $class;
    }
}
