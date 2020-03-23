<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface as EMI;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategorieType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CategorieController extends AbstractController
{
    /**
     * @Route("/admin/categorie", name="admin_categorie")
     */
    public function index(CategorieRepository $categorieRepo)
    {
        $liste_categories = $categorieRepo->findAll();   
        return $this->render('categorie/index.html.twig', compact("liste_categories") );
    }

    /**
     * @Route("/admin/categorie/ajouter", name="admin_categorie_ajouter")
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(CategorieRepository $categorieRepo, EMI $em, Request $rq)
    {
        $formCategorie = $this->createForm(CategorieType::class);
        $formCategorie->handleRequest($rq);
        if($formCategorie->isSubmitted()) {
            if($formCategorie->isValid()) {
                $categorie = $formCategorie->getData();
                $nomCategorie = $formCategorie->get('nom')->getData();
                $em->persist($categorie); 
                $em->flush();    
                $this->addFlash("success", "catégorie bien ajoutée à la base");
                return $this->redirectToRoute("admin_categorie");
            } else {
                $this->addFlash("danger", "Le formulaire n'est pas valide");
            }
        }
        $formCategorie = $formCategorie->createView();  
        return $this->render('categorie/formCategorie.html.twig', compact("formCategorie") );
    }

    /**
     * @Route("/admin/categorie/modifier/{id}", name="admin_categorie_modifier", requirements={"id" = "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(CategorieRepository $categorieRepo, EMI $em, Request $rq, int $id)
    {
        $categorieAModifier = $categorieRepo->find($id);
        $formCategorie = $this->createForm(CategorieType::class, $categorieAModifier);
        $formCategorie->handleRequest($rq);
        if($formCategorie->isSubmitted()) {
            if($formCategorie->isValid()) {
                $nomCategorie = $formCategorie->get('nom')->getData();
                $em->persist($categorieAModifier); 
                $em->flush();   
                $this->addFlash("success", "Modification bien enregistrée"); 
                return $this->redirectToRoute("admin_categorie");
            } else {
                $this->addFlash("danger", "Le formulaire n'est pas valide");
            }
        }
        $formCategorie = $formCategorie->createView();  
        return $this->render('categorie/formCategorie.html.twig', ["formCategorie" => $formCategorie, "categorie" => $categorieAModifier, "mode" => "Modifier"] );
    }

    /**
     * @Route("/admin/categorie/supprimer/{id}", name="admin_categorie_supprimer", requirements={"id" = "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(CategorieRepository $categorieRepo, EMI $em, Request $rq, int $id)
    {
        $categorieASupprimer = $categorieRepo->find($id);
        $em->remove($categorieASupprimer); 
        $em->flush();  
        $this->addFlash("success", "catégorie supprimé de la base");  
        return $this->redirectToRoute("admin_categorie");
    }
}
