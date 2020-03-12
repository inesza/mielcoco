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

use App\Entity\Categorie;
use App\Repository\CategorieRepository;

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
    public function add(RecetteRepository $recetteRepo, CompositionRepository $compoRepo, EMI $em, Request $rq)
    {
        $formRecette = $this->createForm(RecetteType::class);
        $formRecette->handleRequest($rq);
        if($formRecette->isSubmitted()) {
            if($formRecette->isValid()) {
            $recette = $formRecette->getData();
            $photoRecette = $formRecette->get('photo')->getData();
            $nomRecette = $formRecette->get('nom')->getData();
            $compositionRecette = $formRecette->getData();
            if ($photoRecette) {
                $filename = $nomRecette .'-'.uniqid().'.'.$photoRecette->guessExtension();
                $photoRecette->move(
                    $this->getParameter('photosRecettes'),
                    $filename
                );
                $recette->setPhoto($filename);
            }
                
                $em->persist($recette); 
                $em->persist($compositionRecette);
                $em->flush();    
                $this->addFlash("success", "Recette bien ajoutée à la base");
                return $this->redirectToRoute("admin_recette");
            } else {
                $this->addFlash("danger", "Le formulaire n'est pas valide");
            }
        }
        $formRecette = $formRecette->createView();  
        return $this->render('recette/formRecette.html.twig', compact("formRecette") );
    }

    /**
     * @Route("/admin/Recette/modifier/{id}", name="admin_Recette_modifier", requirements={"id" = "\d+"})
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
     * @Route("/admin/Recette/supprimer/{id}", name="admin_Recette_supprimer", requirements={"id" = "\d+"})
     */
    public function delete(RecetteRepository $RecetteRepo, EMI $em, Request $rq, int $id)
    {
        $RecetteASupprimer = $RecetteRepo->find($id);
        $photoASupprimer = $RecetteASupprimer->getPhoto();
        if ($photoASupprimer) {
            unlink("../public/images/recettes/" . $photoASupprimer);
        }
        $em->remove($RecetteASupprimer); 
        $em->flush();  
        $this->addFlash("success", "Recette supprimée de la base");  
        return $this->redirectToRoute("admin_recette");

        return $this->render('recette/index.html.twig', ["recette" => $RecetteASupprimer] );
    }
}
