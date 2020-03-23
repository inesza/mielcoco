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
    
    //------------------------------INFORMATIONS DE L'UTILISATEUR--------------------------------------------------

    /**
     * @Route("/user/modifier/{id}", name="user_update" , requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function update(UserRepository $userRepo, Request $request, EMI $em, int $id)
    {
        $bouton = "update";
        $userAmodifier = $userRepo->find($id);
        // $user=  $this->getUser();

        if($request->isMethod("POST")){
            $email = $request->request->get('email');
            $password = trim($request->request->get('password')); // trim supprime les espaces au début et à la fin d'une chaîne de caractères
            if($password){
                $password = password_hash($password, PASSWORD_DEFAULT);
                $userAmodifier->setPassword($password);
            }
            $userAmodifier->setEmail($email);
    
            $em->persist($userAmodifier);//  requête UPDATE
            $em->flush(); // exécute la  requête en attente

            return $this->redirectToRoute("mon_compte"); // redirection vers la route
        }
        return $this->render('client/informations.html.twig', [ "user" => $userAmodifier, "mode" => "modifier" ]);
    }

    /**
     * @Route("/user/supprimer/{id}", name="user_delete", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(UserRepository $userRepo, Request $request, EMI $em, int $id)
    {
        $bouton = "delete";
        $userAsupprimer = $userRepo->find($id);

        if ($request->isMethod("POST")) {
            $em->remove($userAsupprimer);  //  requête DELETE
            $em->flush();  // exécute la  requête en attente
            return $this->redirectToRoute('mon_compte');  // redirection vers la route
        }
        return $this->render('client/informations.html.twig', ["client" => $clientAsupprimer, "bouton" => $bouton]);
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
