<?php

declare(strict_types=1);

namespace App\Controller\Group;

use App\Entity\Group;
use App\Pagination\Paginator;
use App\Security\Service\GroupService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@alpabit.com>
 */
class GetAll extends AbstractFOSRestController
{
    private $service;

    private $paginator;

    public function __construct(GroupService $service, Paginator $paginator)
    {
        $this->service = $service;
        $this->paginator = $paginator;
    }

    /**
     * @Rest\Get("/groups")
     *
     * @SWG\Tag(name="Group")
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="string",
     *     description="Page indicator"
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="string",
     *     description="Records per page"
     * )
     * @SWG\Parameter(
     *     name="q",
     *     in="query",
     *     type="string",
     *     description="Search group by code or name"
     * )
     * @SWG\Parameter(
     *     name="code",
     *     in="query",
     *     type="string",
     *     description="Filter group by code"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Return group list",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Group::class, groups={"read"}))
     *     )
     * )
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     *
     * @return View
     */
    public function __invoke(Request $request): View
    {
        return $this->view($this->paginator->paginate($this->service->getQueryBuilder(), $request, Group::class));
    }
}