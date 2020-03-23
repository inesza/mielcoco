<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface as EMI;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Form\CommandeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class ClientController extends AbstractController
{
    /**
     * @Route("/user", name="mon_compte")
     * @IsGranted("ROLE_USER")
     */
    public function moncompte()
    {
        return $this->render('client/index.html.twig');
    }
    

    /**
     * @Route("/user/modifier/{id}", name="client_update" , requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function update(ClientRepository $clientRepo, Request $request, EMI $em, int $id)
    {
        $bouton = "update";

        $clientAmodifier = $this->getUser()->getClient();
        $formClient = $this->createForm(ClientType::class, $clientAmodifier); // je crée un formulaire basé sur ClientType
        $formClient->handleRequest($request); // je lie le formulaire à la requête HTTP
        if($formClient->isSubmitted()) {
            if($formClient->isValid()) {
            $nomClient=$formClient->get('nom')->getData();
            $prenomClient=$formClient->get('prenom')->getData();
            $adresseClient=$formClient->get('adresse')->getData();
            $cpClient=$formClient->get('cp')->getData();
            $villeClient=$formClient->get('ville')->getData();
            $telephoneClient=$formClient->get('telephone')->getData();
            }
            $em->persist($clientAmodifier);
            $em->flush();
            $this->addFlash("Success", "Vos modifications ont été enregistrées");// je définie le message qui sera affiché 

            return $this->redirectToRoute("home");
            }
        else {
            $this->addFlash("danger", "Le formulaire n'est pas valide");
        }
        $formClient = $formClient->createView();
        return $this->render('client/coordonnees.html.twig', ["client" => $clientAmodifier, "bouton" => $bouton, "formClient" => $formClient]); 
    }

    /**
     * @Route("/user/supprimer/{id}", name="client_delete", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(clientRepository $clientRepo, Request $request,EMI $em, int $id)
    {
        $bouton = "delete";

        $clientAsupprimer = $clientRepo->find($id);
        if ($clientAsupprimer) {
            $em->remove($clientAsupprimer);
            $em->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render('client/coordonnees.html.twig', ["client" => $clientAsupprimer, "bouton" => $bouton]);

    }  
    
     /**
     * @Route("/commande", name="commande")
     * @IsGranted("ROLE_USER")
     */
    public function commande(CommandeRepository $commandeRepo)
    {   
        $client = $this->getUser()->getClient();
        // dd($client);
        $commandes= $commandeRepo->findBy(['client' => $client]);
        // dd($commandes);
        return $this->render('commande/commandeSuivi.html.twig', compact("commandes"));
    }    
}
