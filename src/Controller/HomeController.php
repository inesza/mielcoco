<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Entity\Recette;
use App\Entity\Produit;
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
    public function recherche(ProduitRepository $produitRepo,RecetteRepository $recetteRepo, Request $rq)
    {
        if($rq->isMethod("POST")){
            $nom = $rq->request->get("recherche");
            $liste_recettes = $recetteRepo->findByNom($nom);
            $liste_produits = $produitRepo->findByNom($nom);
        } 
        else {
            $liste_recettes = $recetteRepo->findAll();
            $liste_produits = $produitRepo->findAll();
        }
        return $this->render('home/recherche.html.twig', compact("liste_recettes", "liste_produits"));
    }

    /**
     * @Route("/recette/{nom}", name="recherche_recette")
     */
    public function rechRecette(RecetteRepository $recetteRepo, $nom)
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

    /**
     * @Route("/qui_sommes_nous", name="qui_sommes_nous")
     */
    public function aPropos()
    {
        return $this->render('home/aPropos.html.twig');
    }

    /**
     * @Route("/cosmetique_bio", name="cosmetique_bio")
     */
    public function cosmetiqueB()
    {
        return $this->render('home/cosmetiqueBio.html.twig');
    }
    /**
     * @Route("/cgv_mentions", name="cgv_mentions")
     */
    public function cgv()
    {
        return $this->render('home/cgvMentions.html.twig');
    }

}
