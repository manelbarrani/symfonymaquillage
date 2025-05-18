<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use App\Entity\OrderItem;

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
    // src/Controller/CartController.php
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
            throw $this->createNotFoundException('Product not found');
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
}