<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface as EMI;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends AbstractController
{
    /**
     * @Route("/mon_compte", name="mon_compte")
     */
    public function index(ClientRepository $clientRepo, Request $request, EMI $em)
    {
        if($request->isMethod("POST")){
        }
        else {
            return $this->redirectToRoute("home");
        }
        return $this->render('client/index.html.twig');  
    }
    

    /**
     * @Route("/client/modifier/{id}", name="client_update" , requirements={"id"="\d+"})
     */
    public function update(ClientRepository $clientRepo, Request $request, EMI $em, int $id)
    {
            $bouton = "update";

            $clientAmodifier = $clientRepo->find($id);
            $formClient = $this->createForm(ClientType::class, $clientAmodifier); // je crée un formulaire basé sur ClientType
            $formClient->handleRequest($request); // je lie le formulaire à la requête HTTP
            if ($formClient->isSubmitted() && $formClient->isValid()) {
                $client = $formClient->getData(); // je récupère les informations de mon formulaire 
                // $nom = $request->request->get('nom');
                // $prenom = $request->request->get('prenom');
                // $adresse = $request->request->get('adresse');
                // $cp = $request->request->get('cp');
                // $ville = $request->request->get('ville');
                // $telephone = $request->request->get('telephone');
            // j'enregistre dans la base de données
            $em->persist($clientAmodifier);
            $em->flush();
            $this->addFlash("Success", "Vos modifications ont été enregistrées");// je définie le message qui sera affiché 

            return $this->redirectToRoute("mon_compte");

            }
        return $this->render('panier/tunnel1.html.twig', ["client" => $clientAmodifier, "bouton" => $bouton]); 
        }
    

    

    /**
     * @Route("/client/supprimer/{id}", name="client_delete"))
     */
    public function delete(clientRepository $clientRepo, Request $request,EMI $em, int $id)
    {
        $bouton = "delete";

        $clientAsupprimer = $clientRepo->find($id);
        
        if ($formClient->isSubmitted() && $formClient->isValid()) {
            $em->remove($clientAsupprimer);
            $em->flush();
            return $this->redirectToRoute("mon_compte");
        }
        return $this->render('panier/tunnel1.html.twig', ["client" => $clientAsupprimer, "bouton" => $bouton]);

    }  
    
     /**
     * @Route("/commande/{id}", name="commande"))
     */
    public function commande()
    {
     return $this->redirectToRoute("commande");
    }    


}





