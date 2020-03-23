<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//Dès maintenant la classe EMI fait référence à la classe Doctrine\ORM\EntityManagerInterface
use Doctrine\ORM\EntityManagerInterface as EMI; //Permet de modifier les informations de la BDD
use Symfony\Component\HttpFoundation\Request;// La classe Request permet d'avoir des informations concernant la requête HTTP
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
    
    //------------------------------COORDONNEES---------------------------------------------------------

    /**
     * @Route("/mon_compte/modifier/{id}", name="client_update" , requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function update(ClientRepository $clientRepo, Request $request, EMI $em)
    {
        $bouton = "update";

        $clientAmodifier = $this->getUser()->getClient();
        $formClient = $this->createForm(ClientType::class, $clientAmodifier); // je crée un formulaire basé sur ClientType
        //handleRequest() détecte le moment où le formulaire a été soumis
        $formClient->handleRequest($request); // je lie le formulaire à la requête HTTP
        //submit() contrôle quand exactement le formulaire est soumis et quelles données lui sont transmises.
        //$ form-> isValid () est un raccourci qui demande à l'objet $clientAmodifier object s'il contient ou non des données valides.
        if ($formClient->isSubmitted() && $formClient->isValid()) {
            $em->persist($clientAmodifier);// Enregistrement en BDD
            $em->flush();  // exécute la requête en attente
            $this->addFlash("Success", "Vos modifications ont été enregistrées");// je définie le message qui sera affiché 

            return $this->redirectToRoute("home");// redirection vers la route
        }
        return $this->render('client/coordonnees.html.twig', ["client" => $clientAmodifier, "bouton" => $bouton, "formClient" => $formClient->createView()]); 
        //CreateView () crée un autre objet avec la représentation visuelle du formulaire.
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
    //------------------------------COMMANDES---------------------------------------------------------
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
