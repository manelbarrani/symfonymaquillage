<?php

// src/Controller/AdminController.php
namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Order;
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

    #[Route('/createProduct', name: 'admin_product_new')]
    public function newProduct(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Produit();
        $form = $this->createForm(ProduitType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product created successfully');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/CreatProduct/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

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

    #[Route('/products/edit/{id}', name: 'admin_product_edit')]
    public function editProduct(Request $request, Produit $product, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProduitType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload if using VichUploader
            $em->flush();

            $this->addFlash('success', 'Product updated successfully');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    // Delete route
    #[Route('/products/delete/{id}', name: 'admin_product_delete', methods: ['POST'])]
    public function deleteProduct(Request $request, Produit $product, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
        }
        
        return $this->redirectToRoute('admin_product_index');
    }
     

    // Delete user route
    #[Route('/users/delete/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
        }
        
        return $this->redirectToRoute('admin_user_index');
    }
    
}
