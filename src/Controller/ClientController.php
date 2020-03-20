<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface as EMI;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CommandeRepository;
use App\Form\CommandeType;

class ClientController extends AbstractController
{
    /**
     * @Route("/mon_compte", name="mon_compte")
     */
    public function moncompte()
    {
        return $this->render('client/index.html.twig');
    }
    

    /**
     * @Route("/mon_compte/modifier/{id}", name="client_update" , requirements={"id"="\d+"})
     */
    public function update(ClientRepository $clientRepo, Request $request, EMI $em)
    {
        $bouton = "update";

        $clientAmodifier = $this->getUser()->getClient();
        $formClient = $this->createForm(ClientType::class, $clientAmodifier); // je crée un formulaire basé sur ClientType
        $formClient->handleRequest($request); // je lie le formulaire à la requête HTTP
        if ($formClient->isSubmitted() && $formClient->isValid()) {
            $em->persist($clientAmodifier);
            $em->flush();
            $this->addFlash("Success", "Vos modifications ont été enregistrées");// je définie le message qui sera affiché 

            return $this->redirectToRoute("home");

        }
        return $this->render('client/coordonnees.html.twig', ["client" => $clientAmodifier, "bouton" => $bouton, "formClient" => $formClient->createView()]); 
    }

    /**
     * @Route("/mon_compte/supprimer/{id}", name="client_delete")
     */
    public function delete(clientRepository $clientRepo, Request $request,EMI $em, int $id)
    {
        $bouton = "delete";

        $clientAsupprimer = $clientRepo->find($id);
        if ($formClient->isSubmitted() && $formClient->isValid()) {
            $em->remove($clientAsupprimer);
            $em->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render('client/coordonnees.html.twig', ["client" => $clientAsupprimer, "bouton" => $bouton, "formClient" => $formClient->createView()]);

    }  
    
     /**
     * @Route("/commande", name="commande")
     */
    public function commande(CommandeRepository $commandeRepo)
    {
        $commandes = $commandeRepo->findAll();
        return $this->render('commande/commandeSuivi.html.twig', compact("commandes"));
    }    
}
