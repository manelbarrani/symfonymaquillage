<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Ordre;
use App\Entity\OrdreProduit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'total' => $total
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'app_remove_from_cart')]
    public function removeFromCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/add/{id}', name: 'app_add_to_cart')]
    public function addToCart(
        int $id,
        SessionInterface $session,
        EntityManagerInterface $entityManager
    ): Response {
        $product = $entityManager->getRepository(Produit::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable');
        }

        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => 1
            ];
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_pages');
    }

    #[Route('/cart/checkout', name: 'cart_checkout')]
    public function checkout(
        SessionInterface $session,
        Security $security,
        ProduitRepository $produitRepository,
        EntityManagerInterface $em
    ): Response {
        $cart = $session->get('cart', []);
        if (empty($cart)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        $user = $security->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour valider votre commande.');
            return $this->redirectToRoute('app_login'); // adapter selon votre route de login
        }

        $ordre = new Ordre();
        $ordre->setUser($user);
        $ordre->setCreatedAt(new \DateTime());
        $ordre->setStatus('en cours'); // ou 'confirmé', selon votre logique

        $total = 0;

        foreach ($cart as $id => $item) {
            $produit = $produitRepository->find($id);
            if ($produit) {
                $ordreProduit = new OrdreProduit();
                $ordreProduit->setProduit($produit);
                $ordreProduit->setQuantity($item['quantity']);
                $ordreProduit->setOrdre($ordre); // Très important
                $em->persist($ordreProduit);

                $ordre->addOrdreProduit($ordreProduit);

                $total += $produit->getPrice() * $item['quantity'];
            }
        }

        $ordre->setTotal($total);
        $em->persist($ordre);
        $em->flush();

        $session->remove('cart');
        $this->addFlash('success', 'Achat confirmé ! Merci pour votre commande.');

        return $this->redirectToRoute('app_pages');
    }
}
