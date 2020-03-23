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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RecetteController extends AbstractController
{

//******************************************* ADMINISTRATION RECETTE *********************************/


    /**
     * @Route("/admin/recette", name="admin_recette")
     * 
     */
    public function index(RecetteRepository $recetteRepo, AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
           return $this->redirectToRoute('home');
        } else { // Accès autorisé

            $liste_recettes = $recetteRepo->findAll();   
            return $this->render('recette/index.html.twig', compact("liste_recettes") );    
        }
        
    }

    /**
     * @Route("/admin/recette/ajouter", name="admin_recette_ajouter")
     * 
     */
    public function add(RecetteRepository $recetteRepo, CategorieRepository $catRepo, EMI $em, Request $rq, AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
        } else { // Accès autorisé

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
    }

    /**
     * @Route("/admin/recette/ajouter/compo-{id}", name="admin_recette_ajouter_compo")
     * 
     */
    public function addCompo(RecetteRepository $recetteRepo, CompositionRepository $compoRepo, EMI $em, Request $rq, int $id, AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
         } else { // Accès autorisé
        
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
    }

    /**
     * @Route("/admin/recette/ajouter/compo-{id}/retirer-{idcompo}", name="admin_ingredient_retirer", requirements={"id" = "\d+"})
     * 
     */
    public function removeIngredient(RecetteRepository $recetteRepo, CompositionRepository $compoRepo, EMI $em, int $id, int $idcompo, AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
        } else { // Accès autorisé
            $recette = $recetteRepo->find($id); 
            $em->remove($compoRepo->find($idcompo));
            $em->flush(); 

            $this->addFlash("success", "Ingrédient retiré de la recette");  
            return $this->redirectToRoute("admin_recette_ajouter_compo", compact("id"));
        }
    }

    /**
     * @Route("/admin/recette/ajouter/compo-{id}", name="admin_recette_produitsAjoutes")
     * 
     */
    public function produitsAjoutes(ProduitRepository $produitRepo, CompositionRepository $compoRepo, AuthorizationCheckerInterface $authChecker)
    { 
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
        } else { // Accès autorisé
            $recette = $recetteRepo->find($id);      
            $produitsAjoutes = $recette->getCompositions();
            return $this->render('recette/formCompo.html.twig', compact("produitsAjoutes") );
        }
    }

    

    

    /**
     * @Route("/admin/recette/modifier/{id}", name="admin_recette_modifier", requirements={"id" = "\d+"})
     * 
     */
    public function update(RecetteRepository $RecetteRepo, CategorieRepository $catRepo, EMI $em, Request $rq, int $id, AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
        } else { // Accès autorisé
            $recetteAModifier = $RecetteRepo->find($id);
            $formRecette = $this->createForm(RecetteType::class, $recetteAModifier);
            $formRecette->handleRequest($rq);
            if($formRecette->isSubmitted()) {
                if($formRecette->isValid()) {
                    $anciennePhoto = $recetteAModifier->getPhoto();
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
                        $recetteAModifier->setPhoto($filename);
                    } 
                    
                    $categories = $recetteAModifier->getCategories();
                    foreach ($categories as $categorie){
                        $recetteAModifier->removeCategory($categorie);    
                    }
                    $idCategories = $rq->request->get('recette')['categorie'];
                    foreach ($idCategories as $id) {
                        $recetteAModifier->addCategory($catRepo->find($id));
                    }
                    $em->persist($recetteAModifier); 
                    $em->flush();   
                    $this->addFlash("success", "Modification bien enregistrée"); 
                    return $this->redirectToRoute("admin_recette");
                } else {
                    $this->addFlash("danger", "Le formulaire n'est pas valide");
                }
            }
            $formRecette = $formRecette->createView();  
            return $this->render('recette/formRecette.html.twig', ["formRecette" => $formRecette, "recette" => $recetteAModifier, "mode" => "Modifier"] );
        }
    }

    /**
     * @Route("/admin/recette/supprimer/{id}", name="admin_recette_supprimer", requirements={"id" = "\d+"})
     * 
     */
    public function delete(RecetteRepository $recetteRepo, CompositionRepository $compoRepo, EMI $em, Request $rq, int $id, AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
        } else { // Accès autorisé
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
    }

    /**
     * @Route("/admin/recette/{id}", name="admin_recette_detail", requirements={"id"="\d+"}) 
     */
    public function recette_detail(RecetteRepository $recetteRepo, CompositionRepository $compoRepo, ProduitRepository $produitRepo, EMI $em, int $id, Request $rq, AuthorizationCheckerInterface $authChecker) {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
        } else { // Accès autorisé
            $recette = $recetteRepo->find($id);      
            $composition = $recette->getCompositions();
            $recette->getPrixRecette();

            return $this->render("recette/recette_detail.html.twig", compact("recette", "composition"));   
        }
    }
}
