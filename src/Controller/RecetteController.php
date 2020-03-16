<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface as EMI;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RecetteType;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

use App\Entity\Composition;
use App\Repository\CompositionRepository;
use App\Form\CompositionType;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;

use App\Entity\Produit;
use App\Repository\ProduitRepository;

class RecetteController extends AbstractController
{
    /**
     * @Route("/admin/recette", name="admin_recette")
     */
    public function index(RecetteRepository $recetteRepo)
    {
        $liste_recettes = $recetteRepo->findAll();   
        return $this->render('recette/index.html.twig', compact("liste_recettes") );
    }

    /**
     * @Route("/admin/recette/ajouter", name="admin_recette_ajouter")
     */
    public function add(RecetteRepository $recetteRepo, CategorieRepository $catRepo, EMI $em, Request $rq)
    {
        $formRecette = $this->createForm(RecetteType::class);
        $formRecette->handleRequest($rq);
        if($formRecette->isSubmitted()) {
            if($formRecette->isValid()) {
            $recette = $formRecette->getData();
            $nomRecette = $formRecette->get('nom')->getData();
        
            $idCategories = $rq->request->get('recette')['categorie'];
            foreach ($idCategories as $id) {
                 $recette->addCategory($catRepo->find($id));
            }

            $photoRecette = $formRecette->get('photo')->getData();
            if ($photoRecette) {
                $filename = $nomRecette .'-'.uniqid().'.'.$photoRecette->guessExtension();
                $photoRecette->move(
                    $this->getParameter('photosRecettes'),
                    $filename
                );
                $recette->setPhoto($filename);
            }
                
                $em->persist($recette);
                
                $em->flush();
                $this->addFlash("success", "Recette bien ajoutée. Ajoutez les ingrédients.");
                return $this->redirectToRoute("admin_recette");
            } else {
                $this->addFlash("danger", "Le formulaire n'est pas valide");
            }
        }
        $formRecette = $formRecette->createView();  
        return $this->render('recette/formRecette.html.twig', compact("formRecette") );
    }

    /**
     * @Route("/admin/recette/ajouter/compo-{id}", name="admin_recette_ajouter_compo")
     */
    public function addCompo(RecetteRepository $recetteRepo, CompositionRepository $compoRepo, EMI $em, Request $rq, int $id)
    {
        
        $recette = $recetteRepo->find($id);
        $formCompo = $this->createForm(CompositionType::class);
        $formCompo->handleRequest($rq);
        if($formCompo->isSubmitted()) {
            if($formCompo->isValid()) {
                $compo = $formCompo->getData();
                $compo->setRecette($recette);
                $em->persist($compo); 
                $em->flush(); 
                
                $this->addFlash("success", "Ingrédient bien ajouté.");
            } else {
                $this->addFlash("danger", "Le formulaire n'est pas valide");
            }
        }
        $formCompo = $formCompo->createView();  
        return $this->render('recette/formCompo.html.twig', compact("formCompo", "recette") );
    }

    /**
     * @Route("/admin/recette/ajouter/compo-{id}", name="admin_recette_produitsAjoutes")
     */
    public function produitsAjoutes(ProduitRepository $produitRepo, CompositionRepository $compoRepo)
    {
        $compoEnCours = $recetteRepo->find($id);
        $produitsAjoutes = $produitRepo->findAll();   
        return $this->render('recette/formCompo.html.twig', compact("produitsAjoutes") );
    }

    

    /**
     * @Route("/admin/Recette/modifier/{id}", name="admin_recette_modifier", requirements={"id" = "\d+"})
     */
    public function update(RecetteRepository $RecetteRepo, EMI $em, Request $rq, int $id)
    {
        $RecetteAModifier = $RecetteRepo->find($id);
        $formRecette = $this->createForm(RecetteType::class, $RecetteAModifier);
        $formRecette->handleRequest($rq);
        if($formRecette->isSubmitted()) {
            if($formRecette->isValid()) {
                $anciennePhoto = $RecetteAModifier->getPhoto();
                $photoRecette = $formRecette->get('photo')->getData();
                $nomRecette = $formRecette->get('nom')->getData();
                if ($photoRecette) {
                    if ($anciennePhoto) {
                        unlink("../public/images/recettes/" . $anciennePhoto);
                    }
                    $filename = $nomRecette .'-'.uniqid().'.'.$photoRecette->guessExtension();
                    $photoRecette->move(
                        $this->getParameter('photosRecettes'),
                        $filename
                    );
                    $RecetteAModifier->setPhoto($filename);
                }
                $em->persist($RecetteAModifier); 
                $em->flush();   
                $this->addFlash("success", "Modification bien enregistrée"); 
                return $this->redirectToRoute("admin_recette");
            } else {
                $this->addFlash("danger", "Le formulaire n'est pas valide");
            }
        }
        $formRecette = $formRecette->createView();  
        return $this->render('recette/formRecette.html.twig', ["formRecette" => $formRecette, "recette" => $RecetteAModifier, "mode" => "Modifier"] );
    }

    /**
     * @Route("/admin/Recette/supprimer/{id}", name="admin_recette_supprimer", requirements={"id" = "\d+"})
     */
    public function delete(RecetteRepository $recetteRepo, CompositionRepository $compoRepo, EMI $em, Request $rq, int $id)
    {
        $recetteASupprimer = $recetteRepo->find($id);

        $photoASupprimer = $recetteASupprimer->getPhoto();
        if (file_exists("../public/images/recettes/" . $photoASupprimer)) {
            unlink("../public/images/recettes/" . $photoASupprimer);
        }
        $em->remove($recetteASupprimer); 
        $em->flush();  
        $this->addFlash("success", "Recette supprimée de la base");  
        return $this->redirectToRoute("admin_recette");
    }

    /**
     * @Route("/admin/Recette/{id}", name="admin_recette_detail", requirements={"id"="\d+"}) 
     */
    public function recette_detail(RecetteRepository $recetteRepo, CompositionRepository $compoRepo, ProduitRepository $produitRepo, EMI $em, int $id, Request $rq) {
        $recette = $recetteRepo->find($id);      
        $composition = $recette->getCompositions();

        return $this->render("recette/recette_detail.html.twig", compact("recette", "composition"));   
    }

    /**
     * @Route("/Recette/{id}", name="recette_fiche", requirements={"id"="\d+"}) 
     */
    public function recette_fiche(RecetteRepository $recetteRepo, EMI $em, int $id, Request $rq) {
        $recette = $recetteRepo->find($id);      
        $composition = $recette->getCompositions();
        $categorie = $recette->getCategories();

        return $this->render("recette/recette_fiche.html.twig", compact("recette"));   
    }
}
