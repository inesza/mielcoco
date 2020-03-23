<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface as EMI;

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
        $best = $catRepo->findBy(["nom" => "Bestsellers"]);
        $bestsellers = $best[0]->getRecettes();  

        foreach ($bestsellers as $best => $recette){
            $compo = $recetteRepo->find($recette)->getCompositions();
            $recette->getPrixRecette();
        }
        
        return $this->render('home/home.html.twig', compact("bestsellers", "compo"));
    }
 //------------------------------RECHERCHE RECETTE -----------------------------
    /**
     * @Route("/recherche", name="recherche")
     */
    public function recherche(RecetteRepository $recetteRepo, Request $rq)
    {   //  ->request: permet de récupérer ce qui se trouve dans $_POST
        if($rq->isMethod("POST")){ // Récupération des données envoyées par le formulaire
            $nom = $rq->request->get("recherche");  //cela équivaut à: $nom = $_POST["name"]
            $liste_recettes = $recetteRepo->findByNom($nom);
        } 
        else {
            $liste_recettes = $recetteRepo->findAll();
        }
        return $this->render('home/recherche.html.twig', compact("liste_recettes"));
    }

    /**
     * @Route("/recette/{nom}", name="recherche_recette")
     */
    public function rechRecette(RecetteRepository $recetteRepo, $nom)
    {
        $liste_recettes = $recetteRepo->findByNom($nom);
        return $this->render('home/listeRecettes.html.twig', compact("liste_recettes")); 
        // compact("liste_recettes", "autre_variable") 
        // est équivalent à 
        // [ "liste_recettes" => $liste_recettes, "autre_variable" => $autre_variable ] 
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
        foreach ($categVisage as $recette){
            $recette->getPrixRecette();
        }

        return $this->render('categories/visage.html.twig', compact("categVisage") );
    }


    /**
     * @Route("/cheveux", name="cheveux")
     */
    public function categCheveux(RecetteRepository $recetteRepo, CategorieRepository $catRepo)
    {
        $cheveux = $catRepo->findBy(["nom" => "Cheveux"]);
        $categCheveux= $cheveux[0]->getRecettes(); 
        foreach ($categCheveux as $recette){
            $recette->getPrixRecette();
        }

        return $this->render('categories/cheveux.html.twig', compact("categCheveux") );
    }

    /**
     * @Route("/corps", name="corps")
     */
    public function categCorps(RecetteRepository $recetteRepo, CategorieRepository $catRepo)
    {
        $corps = $catRepo->findBy(["nom" => "Corps"]);
        $categCorps= $corps[0]->getRecettes();  
        foreach ($categCorps as $recette){
            $recette->getPrixRecette();
        }

        return $this->render('categories/corps.html.twig', compact("categCorps") );
    }

        /**
     * @Route("/accessoires", name="accessoires")
     */
    public function categAccessoires(RecetteRepository $recetteRepo, CategorieRepository $catRepo)
    {
        $accessoires = $catRepo->findBy(["nom" => "Accessoires"]);
        $categAccessoires= $accessoires[0]->getRecettes(); 
        foreach ($categAccessoires as $recette){
            $recette->getPrixRecette();
        }
 

        return $this->render('categories/accessoires.html.twig', compact("categAccessoires") );
    }

    /**
     * @Route("/recettes", name="recettes")
     */
    public function recettes(RecetteRepository $recetteRepo)
    {
        $liste_recettes = $recetteRepo->findAll();  
        foreach ($liste_recettes as $recette){
            $recette->getPrixRecette();
        } 
        return $this->render('categories/toutesRecettes.html.twig', compact("liste_recettes") );
    }

    /**
     * @Route("/detail/recette/{id}", name="recette_fiche", requirements={"id"="\d+"}) 
     * 
     */
    public function recette_fiche(RecetteRepository $recetteRepo, EMI $em, int $id, Request $rq) {
        $recette = $recetteRepo->find($id);      
        $composition = $recette->getCompositions();
        $categorie = $recette->getCategories();
        $recette->getPrixRecette();

        return $this->render("recette/recette_fiche.html.twig", compact("recette"));   
    }

}
