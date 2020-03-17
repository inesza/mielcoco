<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Entity\Recette;
use App\Repository\RecetteRepository;
use App\Repository\ProduitRepository;

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

    /**
     * @Route("/recherche", name="recherche")
     */
    public function recherche(RecetteRepository $recetteRepo, Request $rq)
    {
        if($rq->isMethod("POST")){
            $nom = $rq->request->get("recherche");
            $liste_recettes = $recetteRepo->findByNom($nom);
            // return $this->redirectToRoute("recherche_recette");  // redirection vers la route recherche_recette
        } 
        else {
            $liste_recettes = $recetteRepo->findAll();
        }
        return $this->render('home/listeRecettes.html.twig', compact("liste_recettes"));
    }

    /**
     * @Route("/recette/{nom}", name="recherche_recette")
     */
    public function rechProd(RecetteRepository $recetteRepo, $nom)
    {
        $liste_recettes = $recetteRepo->findByNom($nom);
        return $this->render('home/listeRecettes.html.twig', compact("liste_recettes"));
    }
    /**
     * @Route("/produit/{nom}", name="recherche_produit")
     */
    public function rechProduit(ProduitRepository $produitRepo, $nom)
    {
        $liste_produits = $produitRepo->findByNom($nom);
        return $this->render('home/listeProduits.html.twig', compact("liste_produits"));
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


 //------------------------------MENU---------------------------------------------------------
    /**
     * @Route("/visage", name="visage")
     */
    public function categVisage(RecetteRepository $recetteRepo, CategorieRepository $catRepo)
    {
        $categorie = 3;
        $visage = $catRepo->find($categorie);
        $categVisage= $visage->getRecettes(); 

        return $this->render('categories/visage.html.twig', compact("categVisage") );
    }


    /**
     * @Route("/cheveux", name="cheveux")
     */
    public function categCheveux(RecetteRepository $recetteRepo, CategorieRepository $catRepo)
    {
        $categorie = 2;
        $cheveux = $catRepo->find($categorie);
        $categCheveux= $cheveux->getRecettes(); 

        return $this->render('categories/cheveux.html.twig', compact("categCheveux") );
    }

    /**
     * @Route("/corps", name="corps")
     */
    public function categCorps(RecetteRepository $recetteRepo, CategorieRepository $catRepo)
    {
        $categorie = 1;
        $corps = $catRepo->find($categorie);
        $categCorps= $corps->getRecettes(); 

        return $this->render('categories/corps.html.twig', compact("categCorps") );
    }

}
