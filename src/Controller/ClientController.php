<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//Dès maintenant la classe EMI fait référence à la classe Doctrine\ORM\EntityManagerInterface
use Doctrine\ORM\EntityManagerInterface as EMI; //Permet de modifier les informations de la BDD
use Symfony\Component\HttpFoundation\Request;// La classe Request permet d'avoir des informations concernant la requête HTTP
use App\Entity\Client;
use App\Repository\ClientRepository;//Permet de récupérer les informations dans la BDD
use App\Form\ClientType;//Permet d'accéder au formulaire
use App\Entity\Commande;
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
    
    //------------------------------COORDONNEES---------------------------------------------------------

    /**
     * @Route("/mon_compte/modifier/{id}", name="client_update" , requirements={"id"="\d+"})
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
    //------------------------------COMMANDES---------------------------------------------------------
     /**
     * @Route("/commande", name="commande")
     */
    public function commande(CommandeRepository $commandeRepo)
    {
        $commandes = $commandeRepo->findAll();
        return $this->render('commande/commandeSuivi.html.twig', compact("commandes"));
    }    
}
