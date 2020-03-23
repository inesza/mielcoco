<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface as EMI;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProduitType;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

//********************************************** ADMINISTRATION PRODUITS ***********************/

class ProduitController extends AbstractController
{
    /**
     * @Route("/admin/produit", name="admin_produit")
     *
     */
    public function index(ProduitRepository $produitRepo, AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
        } else { // Accès autorisé
            $liste_produits = $produitRepo->findAll();   
            return $this->render('produit/index.html.twig', compact("liste_produits") );
        }
    }

    /**
     * @Route("/admin/produit/ajouter", name="admin_produit_ajouter")
     * 
     */
    public function add(ProduitRepository $produitRepo, EMI $em, Request $rq, AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
        } else { // Accès autorisé
            $formProduit = $this->createForm(ProduitType::class);
            $formProduit->handleRequest($rq);
            if($formProduit->isSubmitted()) {
                if($formProduit->isValid()) {
                $produit = $formProduit->getData();
                $photoProduit = $formProduit->get('photo')->getData();
                $nomProduit = $formProduit->get('nom')->getData();
                if ($photoProduit) {
                    $filename = $nomProduit .'-'.uniqid().'.'.$photoProduit->guessExtension();
                    $photoProduit->move(
                        $this->getParameter('photosProduits'),
                        $filename
                    );
                    $produit->setPhoto($filename);
                }
                    $em->persist($produit); 
                    $em->flush();    
                    $this->addFlash("success", "produit bien ajouté à la base");
                    return $this->redirectToRoute("admin_produit");
                } else {
                    $this->addFlash("danger", "Le formulaire n'est pas valide");
                }
            }
            $formProduit = $formProduit->createView();  
            return $this->render('produit/formProduit.html.twig', compact("formProduit") );
        }
    }

    /**
     * @Route("/admin/produit/modifier/{id}", name="admin_produit_modifier", requirements={"id" = "\d+"})
     * 
     */
    public function update(ProduitRepository $produitRepo, EMI $em, Request $rq, int $id, AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
        } else { // Accès autorisé
            $produitAModifier = $produitRepo->find($id);
            $formProduit = $this->createForm(ProduitType::class, $produitAModifier);
            $formProduit->handleRequest($rq);
            if($formProduit->isSubmitted()) {
                if($formProduit->isValid()) {
                    $anciennePhoto = $produitAModifier->getPhoto();
                    $photoProduit = $formProduit->get('photo')->getData();
                    $nomProduit = $formProduit->get('nom')->getData();
                    if ($photoProduit) {
                        if ($anciennePhoto) {
                            unlink("../public/images/produits/" . $anciennePhoto);
                        }
                        $filename = $nomProduit .'-'.uniqid().'.'.$photoProduit->guessExtension();
                        $photoProduit->move(
                            $this->getParameter('photosProduits'),
                            $filename
                        );
                        $produitAModifier->setPhoto($filename);
                    }
                    $em->persist($produitAModifier); 
                    $em->flush();   
                    $this->addFlash("success", "Modification bien enregistrée"); 
                    return $this->redirectToRoute("admin_produit");
                } else {
                    $this->addFlash("danger", "Le formulaire n'est pas valide");
                }
            }
            $formProduit = $formProduit->createView();  
            return $this->render('produit/formProduit.html.twig', ["formProduit" => $formProduit, "produit" => $produitAModifier, "mode" => "Modifier"] );
        }
    }

    /**
     * @Route("/admin/produit/supprimer/{id}", name="admin_produit_supprimer", requirements={"id" = "\d+"})
     * 
     */
    public function delete(ProduitRepository $produitRepo, EMI $em, Request $rq, int $id, AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) { // contrôle d'accès
            $this->addFlash("danger", "Vous devez être administrateur·ice pour voir cette page");
            return $this->redirectToRoute('home');
        } else { // Accès autorisé
            $produitASupprimer = $produitRepo->find($id);
            $photoASupprimer = $produitASupprimer->getPhoto();
            if ($photoASupprimer) {
                unlink("../public/images/produits/" . $photoASupprimer);
            }
            $em->remove($produitASupprimer); 
            $em->flush();  
            $this->addFlash("success", "produit supprimé de la base");  
            return $this->redirectToRoute("admin_produit");
        }
    }
}
