<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function home()
    {
        return $this->render('base.twig');
    }

    /**
     * @Route("/phpinfo")
     */
    public function debug()
    {
        $phpInfo = phpinfo();
        return new Response($phpInfo);

    }
}
