<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface as EMI;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CommandeRepository;
use App\Repository\ClientRepository;
use App\Entity\Commande;
use App\Entity\Client;
use App\Form\CommandeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CommandeController extends AbstractController
{
    /**
     * @Route("/admin/commande", name="liste_commande")
     * @IsGranted("ROLE_ADMIN")
     */
    public function liste(CommandeRepository $cr)
    {
        $commandes = $cr->findAll();
    
        $taille = count($commandes);
        return $this->render('commande/index.html.twig', [
            "commandes" => $commandes, "taille" => $taille
        ]);
    }

    /**
     * @Route("/admin/commande/modifier/{id}", name="commande_modifier", requirements={"id" = "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */

    public function modifier(CommandeRepository $cr, EMI $em, Request $rq, int $id)
    {
        $commandeModif = $cr->find($id);
        $dateCommande = $commandeModif->getDate();
        $formCommande = $this->createForm(CommandeType::class, $commandeModif);
        $commandeModif->setDate($dateCommande);

        $formCommande->handleRequest($rq);
        if($formCommande->isSubmitted()) {
            if($formCommande->isValid()) {
                $em->persist($commandeModif); 
                $em->flush();   
                $this->addFlash("success", "Modification bien enregistrée"); 
                return $this->redirectToRoute("liste_commande");
            } else {
                $this->addFlash("danger", "Le formulaire n'est pas valide");
            }
        }
        $formCommande = $formCommande->createView();  
        return $this->render('commande/formCommande.html.twig', ["formCommande" => $formCommande, "commande" => $commandeModif] );
    }
}
