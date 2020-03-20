<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface as EMI;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{


    /**
     * @Route("/admin/user/{id}", name="user_show", requirements={"id"="\d+"})
     */
    public function show(UserRepository $ur, $id)
    {
        $user = $ur->find($id);
        return $this->render('user/fiche.html.twig', [ "user" => $user ]);
    }

    /**
     * @Route("/admin/user/list", name="user_list")
     */
    public function liste(UserRepository $ur)
    {
        $users = $ur->findAll();
        $taille = count($users);
        return $this->render('user/liste.html.twig', [ "users" => $users, 
                                                         "taille" => $taille,
                                                        ]);
    }

    /**
     * @Route("/admin/user/modifier/{id}", name="user_update")
     */
    public function update(UserRepository $ur, Request $rq, EMI $em, int $id)
    {
        // UserRepository : va servir à récupérer dans la BDD les informations du membre que l'on veut modifier
        // Request : va servir à récupérer les informations venant du formulaire
        // EntityManagerInterface(EMI) : va servir à enregistrer les modifications en BDD
        $userAmodifier = $ur->find($id);
        if($rq->isMethod("POST")){
            $userAmodifier->setEmail($rq->request->get("email"));
            $userAmodifier->setRoles(array($rq->request->get("role")));
            $em->persist($userAmodifier);  // équivalent à la création d'une requête UPDATE
            $em->flush();            // exécute la (ou les) requête(s) en attente
            return $this->redirectToRoute("user_list");  // redirection vers la route
        }
        return $this->render("user/formulaire.html.twig", [ "user" => $userAmodifier, "mode" => "modifier" ]);

    }

    /**
     * @Route("/admin/user/supprimer/{id}", name="user_delete")
     */
    public function delete(UserRepository $ur, Request $rq, EMI $em, int $id)
    {
        $userAsupprimer = $ur->find($id);
        if($userAsupprimer){
            $em->remove($userAsupprimer);    // équivalent à la création d'une requête DELETE
            $em->flush();                       // exécute la (ou les) requête(s) en attente
            return $this->redirectToRoute("user_list");  // redirection vers la route
        }
    }

    /**
     * @Route("/admin/user/ajouter", name="user_add")
     */
    public function add(EMI $em, Request $rq)
    {
    

        if($rq->isMethod("POST")){ 
            $email = $rq->request->get('email');
            $mdp = $rq->request->get('password');
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
            $roles = $rq->request->get('roles');
            
            $user = new User; 
            $user->setEmail($email);
            $user->setPassword($mdp);
            $user->setRoles(array($rq->request->get("role")));

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("user_list");

        }else{
            return $this->render('user/ajout.html.twig'); 
        }
    }

}
