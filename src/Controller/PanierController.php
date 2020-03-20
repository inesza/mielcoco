<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Repository\RecetteRepository;
use App\Repository\ProduitRepository;
use App\Repository\CompositionRepository;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;

use App\Form\ClientType;

use Doctrine\ORM\EntityManagerInterface as EMI;

use App\Entity\Commande;



class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(SessionInterface $session, RecetteRepository $recetteRepo, ProduitRepository $produitRepo, CompositionRepository $compoRepo, Request $rq)
    {
        $panier = $session->get('panier', []); // Si le panier est vide : tableau vide)
        $panierWithData = [];
        
        foreach ($panier as $id => $quantite){
            $compo = $recetteRepo->find($id)->getCompositions();
            
            $prixRecette = 0;
            foreach ($compo as $ligne) {
                $prixProduit = $ligne->getProduit()->getPrixUnitaire();
                $qteProduit = $ligne->getQuantite(); 
                $prixRecette += ($prixProduit * $qteProduit);                
            }

            $panierWithData[] = [
                'recette' => $recetteRepo->find($id),
                'quantite' => $quantite,
                'prix' => $prixRecette
            ];
        }
        $totalPanier = 0;
        foreach ($panierWithData as $item) {
            $prixRecetteQte = $item['prix'] * $item['quantite'];
            $totalPanier += $prixRecetteQte;

        }
        // Calcul des frais de ports (offerts à partir de 49€)
        if ($totalPanier > 49) {
            $fdp = 0;
        } else {
            $fdp = 5;
        }   

        if( $rq->isMethod("POST") ) {
            $codePromo = $rq->request->get("code_promo") ;
            if ( $codePromo = "WELCOME10") {
                $session->remove('montantCommande');
                $totalCommande = $totalPanier + $fdp - 10;
            } else if ( $codePromo = "NEWSLETTER") {
                $session->remove('montantCommande');
                $totalCommande = $totalPanier;
            } else if ($codePromo = "10POURCENT") {
                $session->remove('montantCommande');
                $totalCommande = ($totalPanier * 10/100) + $fdp;
                dd($totalCommande);
            } else {
                $session->remove('montantCommande');
                $totalCommande = $totalPanier + $fdp;   
            }
            $montantCommande[] = [
                'totalCommande' => $totalCommande
            ];
    
            $session->set('montantCommande', $montantCommande);
        } else {
            $totalCommande = $totalPanier + $fdp;
            $montantCommande[] = [
                'totalCommande' => $totalCommande
            ];
    
            $session->set('montantCommande', $montantCommande);
        }

        // $totalCommande = $totalPanier + $fdp;
        // $montantCommande[] = [
        //     'totalCommande' => $totalCommande
        // ];

        // $session->set('montantCommande', $montantCommande);

        return $this->render('panier/index.html.twig', [ 'items' => $panierWithData, "totalPanier" => $totalPanier, "fdp" => $fdp, "totalCommande" => $totalCommande ]);
    }

    /**
     * @Route("/panier/add/{id}", name="ajout_panier")
     */
    public function ajout_panier( int $id, SessionInterface $session)
    {
        $panier = $session->get('panier', []); // Si je n'ai pas de panier, j'en crée un sous forme de tableau vide
        
        if(!empty($panier[$id])) {
           $panier[$id]++; // Je rajoute une unité du produit dans le panier s'il y est déjà
        } else {
           $panier[$id] = 1; // Je rajoute le produit (avec une unité)
        }

        $session->set('panier', $panier); // Je mets à jour mon panier

        return $this->redirectToRoute("panier");
    }
    

    /**
     * @Route("/panier/baisse/{id}", name="baisse_panier")
     */
    public function baisse_panier( int $id, SessionInterface $session)
    {
        $panier = $session->get('panier', []); // Si je n'ai pas de panier, j'en crée un sous forme de tableau vide
        
        if( $panier[$id] > 1) {
           $panier[$id]--; // S'il y a plus d'une unité, j'en enlève une
        } else {
           unset($panier[$id]); // S'il y a exactement une unité, je supprime la recette du panier 
        }

        $session->set('panier', $panier); // Je mets à jour mon panier

        return $this->redirectToRoute("panier");
    }

    /**
     * @Route("/panier/supprimer/{id}", name="supprimer_panier")
     */
    public function supprimer_panier( int $id, SessionInterface $session)
    {
        $panier = $session->get('panier', []); // Si je n'ai pas de panier, j'en crée un sous forme de tableau vide
        
        if(!empty($panier[$id])) {
           unset($panier[$id]); // Je retire la recette du panier
        } 

        $session->set('panier', $panier); // Je mets à jour mon panier

        return $this->redirectToRoute("panier");
    }

    /**
     * @Route("/panier/commander/adresse", name="adresse_panier")
     */
    public function adresse( SessionInterface $session, ClientRepository $clientRepo, EMI $em, Request $rq)
    {
        $panier = $session->get('panier'); // Si je n'ai pas de panier, j'en crée un sous forme de tableau vide
        $user = $this->getUser();

        if(!empty($panier)) {
                $formClient = $this->createForm(ClientType::class, $user->getClient());
                $formClient->handleRequest($rq);
            if($formClient->isSubmitted()) {
                if($formClient->isValid()) {
                $client = $formClient->getData();
                $client->setIdUser($user);
                $em->persist($client); 
                $em->flush();    
                return $this->redirectToRoute("paiement_panier");
            }
        }
        $formClient = $formClient->createView();  
        } else {
            return $this->redirectToRoute("home");
        }

        return $this->render('panier/tunnel1.html.twig', [ 'formClient' => $formClient ]);
    }

     /**
     * @Route("/panier/commander/paiement", name="paiement_panier")
     */
    public function paiement( SessionInterface $session, CommandeRepository $commandeRepo, Request $rq, EMI $em)
    {
        if( $rq->isMethod("POST") ) {
            $commande = new Commande;
            $montantCommande = $session->get('montantCommande');

            $client = $this->getUser()->getClient();
            $commande->setClient($client);
            $commande->setDate(new \DateTime('now'));
            $commande->setEtat("En cours de traitement");
            $commande->setMontant($montantCommande[0]['totalCommande']);
            $em->persist($commande);
            $em->flush();


            return $this->redirectToRoute("confirmation_panier"); 
        }
        return $this->render('panier/tunnel2.html.twig');
    }

     /**
     * @Route("/panier/commander/confirmation", name="confirmation_panier")
     */
    public function confirmation(SessionInterface $session)
    {
        $session->remove('panier'); // Vide le panier une fois que la commande est confirmée
        return $this->render('panier/tunnel3.html.twig');
    }

    

    
}
