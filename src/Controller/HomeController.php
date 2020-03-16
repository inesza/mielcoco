<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Entity\Recette;
use App\Repository\RecetteRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(RecetteRepository $recetteRepo, CategorieRepository $catRepo)
    {
        // return $this->render('home/home.html.twig', [
        //     'controller_name' => 'HomeController',
        // ]);
        $categorie = 5;
        $best = $catRepo->find($categorie);
        $bestsellers = $best->getRecettes();  
        
        return $this->render('home/home.html.twig', compact("bestsellers"));
    }

    // /**
    //  * @Route("/", name="bestsellers")
    //  */
    // public function bestsellers(RecetteRepository $recetteRepo, CategorieRepository $catRepo, Request $rq)
    // {
    //     // $best = $catRepo->find('Bestsellers');
    //     // $bestsellers = $best->getRecettes();
    //     $recettes = $recetteRepo->findAll();
    //     return $this->render('home/home.html.twig', compact("recettes"));


    //     // return $this->render('home/home.html.twig', compact("bestsellers"));
    // }

    // public function bestsellers(RecetteRepository $recetteRepo, Request $rq)
    // {
    //     $recettes = $recetteRepo->findAll();   
        
    //     return $this->render('home/home.html.twig', compact("recettes"));
    // }

}
