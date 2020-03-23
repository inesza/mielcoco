<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;// La classe Request permet d'avoir des informations concernant la requête HTTP
use App\Entity\Categorie; //Entity: est égal à un objet
use App\Repository\CategorieRepository;//Permet de récupérer les informations dans la BDD
use App\Entity\Recette;
use App\Repository\RecetteRepository;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Entity\Composition;
use App\Repository\CompositionRepository;


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
 //------------------------------RECHERCHE RECETTE -----------------------------
    /**
     * @Route("/recherche", name="recherche")
     */
    public function recherche(ProduitRepository $produitRepo, RecetteRepository $recetteRepo, Request $rq)
    {   //  ->request: permet de récupérer ce qui se trouve dans $_POST
        if($rq->isMethod("POST")){ // Récupération des données envoyées par le formulaire
            $nom = $rq->request->get("recherche");  //cela équivaut à: $nom = $_POST["name"]
            $liste_recettes = $recetteRepo->findByNom($nom);
            // $liste_produits = $produitRepo->findByNom($nom);
        } 
        else {
            $liste_recettes = $recetteRepo->findAll();
            // $liste_produits = $produitRepo->findAll();
        }
        return $this->render('home/recherche.html.twig', compact("liste_recettes"
        // , "liste_produits"
        )); 
        // compact("liste_recettes", "autre_variable") 
        // est équivalent à 
        // [ "liste_recettes" => $liste_recettes, "autre_variable" => $autre_variable ] 
    }
//------------------------------RECHERCHE RECETTE PAR PRODUIT-----------------------------
    // /**
    //  * @Route("/recette/produit/{nom}", name="recherche_produit")
    //  */
    // public function rechProduit(compositionRepository $compoRepo, $nom)
    // {
    //     $liste_recettes = $compoRepo->findByNom($nom);
    //     return $this->render('home/recherche.html.twig', compact("liste_recettes"));
    // }
    
 //------------------------------FOOTER--------------------------------------------------------
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
