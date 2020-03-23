<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//Dès maintenant la classe EMI fait référence à la classe Doctrine\ORM\EntityManagerInterface
use Doctrine\ORM\EntityManagerInterface as EMI; //Permet de modifier les informations de la BDD
use Symfony\Component\HttpFoundation\Request;// La classe Request permet d'avoir des informations concernant la requête HTTP
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Form\ClientType;

class ClientController extends AbstractController
{
    /**
     * @Route("/user", name="mon_compte")
     * @IsGranted("ROLE_USER") 
     */
    public function moncompte()
    {
        $user = $this->getUser();
        return $this->render('client/index.html.twig', compact("user"));
    }
    
    //------------------------------INFORMATIONS DE L'UTILISATEUR--------------------------------------------------

    /**
     * @Route("/user/modifier/{id}", name="client_update" , requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function update(UserRepository $userRepo, Request $request, EMI $em, int $id)
    {
        $userAmodifier = $userRepo->find($id);

        if($request->isMethod("POST")){
            $password = trim($request->request->get('password')); // trim supprime les espaces au début et à la fin d'une chaîne de caractères
            if($password){
                $password = password_hash($password, PASSWORD_DEFAULT);
                $userAmodifier->setPassword($password);
            }
    
            $em->persist($userAmodifier);//  requête UPDATE
            $em->flush(); // exécute la  requête en attente

            $this->addFlash("success", "Votre mot de passe a été modifié");

            return $this->redirectToRoute("mon_compte"); // redirection vers la route
        }
        return $this->render('client/index.html.twig', [ "user" => $userAmodifier, "mode" => "modifier" ]);
    }

    /**
     * @Route("/user/supprimer", name="client_delete")
     * @IsGranted("ROLE_USER")
     */
    public function client_delete(UserRepository $userRepo, Request $request, EMI $em)
    {
        $user = $this->getUser();
        $clientASupprimer = $user->getId();

        $em->remove($clientAsupprimer);  //  requête DELETE
        $em->flush();  // exécute la  requête en attente
        $this->addFlash("success", "Votre compte a été supprimé");
        return $this->redirectToRoute('home');  // redirection vers la route
    }  

    //------------------------------COMMANDES---------------------------------------------------------
     /**
     * @Route("/commande", name="client_commande")
     * @IsGranted("ROLE_USER")
     */
    public function commande(CommandeRepository $commandeRepo)
    {   
        $user = $this->getUser();
        $client = $this->getUser()->getClient();
        // dd($client);
        $commandes= $commandeRepo->findBy(['client' => $client]);
        // dd($commandes);
        return $this->render('client/commandeSuivi.html.twig', compact("commandes", "user"));
    }   
    
    
    // ----------------------- Coordonnées -------------//

    /**
     * @Route("/user/coordonnees", name="client_coordonnees")
     * @IsGranted("ROLE_USER") 
     */
    public function coordonnees(ClientRepository $clientRepo, EMI $em, Request $rq)
    {
        // Si l'utilisateur n'a jamais commandé ou n'a jmaais renseigné ses coordonnées
        $user = $this->getUser();
        $formClient = $this->createForm(ClientType::class);
        $formClient->handleRequest($rq);
        if($formClient->isSubmitted()) {
            if($formClient->isValid()) {
                $client = $formClient->getData();
                $client->setIdUser($user);
                $em->persist($client); 
                $em->flush();  
                $this->addFlash("success", "Vos informations ont bien été enregistrées");
                return $this->redirectToRoute("client_coordonnees");
            } else {
                $this->addFlash("danger", "Le formulaire n'est pas valide");
            }  
        }
        $formClient = $formClient->createView();  
        return $this->render('client/coordonnees.html.twig', compact("user", "formClient"));
    }


    /**
     * @Route("/user/coordonnees/modifier", name="client_coordonnees_modif")
     * @IsGranted("ROLE_USER") 
     */
    public function coordonnees_update( ClientRepository $clientRepo, EMI $em, Request $rq)
    {
        $user = $this->getUser();
        $client = $user->getClient();

        $formClient = $this->createForm(ClientType::class, $client);
        $formClient->handleRequest($rq);
        if($formClient->isSubmitted()) {
            if($formClient->isValid()) {
                $client = $formClient->getData();
                $client->setIdUser($user);
                $em->persist($client); 
                $em->flush();  
                $this->addFlash("success", "Vos informations ont bien été mises à jour");
                return $this->redirectToRoute("client_coordonnees");
            } else {
                $this->addFlash("danger", "Le formulaire n'est pas valide");
            }  
        }
        $formClient = $formClient->createView();  

        return $this->render('client/coordonneesModif.html.twig', [ 'formClient' => $formClient, "client" => $client, "user" => $user ]);
    }
}
