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
        $best = $catRepo->findBy(["nom" => "Bestsellers"]);
        $bestsellers = $best[0]->getRecettes();  
        
        return $this->render('home/home.html.twig', compact("bestsellers"));
    }
 //------------------------------RECHERCHE---------------------------------------------------------
    /**
     * @Route("/recherche", name="recherche")
     */
    public function recherche(ProduitRepository $produitRepo, RecetteRepository $recetteRepo, Request $rq)
    {
        if($rq->isMethod("POST")){
            $nom = $rq->request->get("recherche");
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
        $visage = $catRepo->findBy(["nom" => "Visage"]);
        $categVisage= $visage[0]->getRecettes(); 

        return $this->render('categories/visage.html.twig', compact("categVisage") );
    }


    /**
     * @Route("/cheveux", name="cheveux")
     */
    public function categCheveux(RecetteRepository $recetteRepo, CategorieRepository $catRepo)
    {
        $cheveux = $catRepo->findBy(["nom" => "Cheveux"]);
        $categCheveux= $cheveux[0]->getRecettes(); 

        return $this->render('categories/cheveux.html.twig', compact("categCheveux") );
    }

    /**
     * @Route("/corps", name="corps")
     */
    public function categCorps(RecetteRepository $recetteRepo, CategorieRepository $catRepo)
    {
        $corps = $catRepo->findBy(["nom" => "Corps"]);
        $categCorps= $corps[0]->getRecettes(); 

        return $this->render('categories/corps.html.twig', compact("categCorps") );
    }

        /**
     * @Route("/accessoires", name="accessoires")
     */
    public function categAccessoires(RecetteRepository $recetteRepo, CategorieRepository $catRepo)
    {
        $categorie = 4;
        $access = $catRepo->find($categorie);
        $categAccessoires= $access->getRecettes(); 

        return $this->render('categories/accessoires.html.twig', compact("categAccessoires") );
    }

    /**
     * @Route("/recettes", name="recettes")
     */
    public function recettes(RecetteRepository $recetteRepo)
    {
        $liste_recettes = $recetteRepo->findAll();   
        return $this->render('home/toutesRecettes.html.twig', compact("liste_recettes") );
    }

}
