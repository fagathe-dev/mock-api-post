<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    #[ Route( '/api', name: 'app_home' ) ]
    public function home():JsonResponse {
        return $this->json( [
            'msg' => 'Welcome to Mock React Api'
        ] );
    }

}