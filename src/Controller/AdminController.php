<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Ordre;
use App\Form\ProduitType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // ✅ Liste des produits avec pagination
    #[Route('/products', name: 'admin_product_index')]
    public function products(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $em->getRepository(Produit::class)->createQueryBuilder('p');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/product/index.html.twig', [
            'products' => $pagination
        ]);
    }

    // ✅ Création d’un produit
    #[Route('/createProduct', name: 'admin_product_new')]
    public function newProduct(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Produit();
        $form = $this->createForm(ProduitType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/createProduct/index.html.twig', [ // ✅ Corrigé : "createProduct" au lieu de "CreatProduct"
            'form' => $form->createView()
        ]);
    }

    // ✅ Liste des utilisateurs
    #[Route('/users', name: 'admin_user_index')]
    public function users(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $em->getRepository(User::class)->createQueryBuilder('u');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/user/index.html.twig', [
            'users' => $pagination
        ]);
    }

    // ✅ Modifier un produit
    #[Route('/products/edit/{id}', name: 'admin_product_edit')]
    public function editProduct(Request $request, Produit $product, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProduitType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Produit mis à jour avec succès.');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    // ✅ Supprimer un produit
    #[Route('/products/delete/{id}', name: 'admin_product_delete', methods: ['POST'])]
    public function deleteProduct(Request $request, Produit $product, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();

            $this->addFlash('success', 'Produit supprimé.');
        }

        return $this->redirectToRoute('admin_product_index');
    }

    // ✅ Supprimer un utilisateur
    #[Route('/users/delete/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur supprimé.');
        }

        return $this->redirectToRoute('admin_user_index');
    }

    // ✅ Liste des commandes
    #[Route('/orders', name: 'admin_order_index')]
    public function orders(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $em->getRepository(Ordre::class)->createQueryBuilder('o')->orderBy('o.createdAt', 'DESC');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/order/index.html.twig', [
            'orders' => $pagination
        ]);
    }
        #[Route('/admin/order/{id}/delete', name: 'admin_order_delete', methods: ['POST'])]
    public function deleteOrder(Request $request, EntityManagerInterface $em, Ordre $order): RedirectResponse
    {
        // Protection contre les CSRF tokens si tu utilises un formulaire
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $em->remove($order);
            $em->flush();
            $this->addFlash('success', 'Commande supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_order_index'); // adapte selon ta route liste commandes
    }
}
