<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface as EMI;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;


class UserController extends AbstractController
{


    /**
     * @Route("/user/{id}", name="user_show", requirements={"id"="\d+"})
     */
    public function show(UserRepository $ur, $id)
    {
        $user = $ur->find($id);
        return $this->render('user/fiche.html.twig', [ "user" => $user ]);
    }

    /**
     * @Route("/user/list", name="user_list")
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
     * @Route("/user/modifier/{id}", name="user_update")
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
     * @Route("/user/supprimer/{id}", name="user_delete")
     */
    public function delete(UserRepository $ur, Request $rq, EMI $em, int $id)
    {
        $userAsupprimer = $ur->find($id);
        if($rq->isMethod("POST")){
            $em->remove($userAsupprimer);    // équivalent à la création d'une requête DELETE
            $em->flush();                       // exécute la (ou les) requête(s) en attente
            return $this->redirectToRoute("user_list");  // redirection vers la route
        }
        return $this->render("user/formulaire.html.twig", [ "user" => $userAsupprimer, "mode" => "confirmer" ]);
    }
}
