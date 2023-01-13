<?php
namespace App\Service;

use App\Repository\UserRepository;
use App\Utils\ServiceTrait;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiUserService 
{

    use ServiceTrait;

    public function __construct(
        private EntityManagerInterface $manager,
        private UserRepository $repository,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer, 
        private PaginatorInterface $paginator
    ){
    }
    
    /**
     * store
     *
     * @return object
     */
    public function store():object 
    {
        return $this->sendJson();
    }

    public function listUser(Request $request):object {
        $data = $this->repository->findAll();

        $users = $this->paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            15
        );

        return $this->sendJson(compact('users'));
    }

}