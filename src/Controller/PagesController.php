<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use App\Repository\MarqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PagesController extends AbstractController
{
    #[Route('/pages', name: 'app_pages')]
    public function index(
        Request $request,
        Security $security,
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        MarqueRepository $marqueRepository
    ): Response {
        // Redirection si pas connecté
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }

        $selectedCategory = $request->query->get('categorie');
        $selectedBrand = $request->query->get('marque');

        $queryBuilder = $produitRepository->createQueryBuilder('p');

        if ($selectedCategory) {
            $queryBuilder->andWhere('p.categorie = :cat')
                         ->setParameter('cat', $selectedCategory);
        }

        if ($selectedBrand) {
            $queryBuilder->andWhere('p.marque = :marque')
                         ->setParameter('marque', $selectedBrand);
        }

        $products = $queryBuilder->getQuery()->getResult();

        // Ajout flags isNew et onSale dans chaque produit
        foreach ($products as $product) {
            $createdAt = $product->getCreatedAt();
            $product->isNew = $createdAt && $createdAt > new \DateTime('-15 days');
            $product->onSale = $product->getOldPrice() > $product->getPrice();
        }

        $categories = $categorieRepository->findAll();
        $brands = $marqueRepository->findAll();

        return $this->render('pages/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'selectedCategory' => $selectedCategory,
            'selectedBrand' => $selectedBrand,
        ]);
    }
        #[Route('/conseilsbeauty', name: 'app_chat')]
    public function chat(): Response
    {
        return $this->render('pages/chat.html.twig');
    }

    #[Route('/categorie/{id}', name: 'categorie_show')]
    public function showCategory(
        Categorie $categorie,
        CategorieRepository $categorieRepository
    ): Response {
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $products = $categorie->getProduits(); // relation dans entité Categorie

        // Ajout flags isNew et onSale dans chaque produit
        foreach ($products as $product) {
            $createdAt = $product->getCreatedAt();
            $product->isNew = $createdAt && $createdAt > new \DateTime('-15 days');
            $product->onSale = $product->getOldPrice() > $product->getPrice();
        }

        // Récupérer toutes les catégories pour l'affichage dans le header/menu
        $categories = $categorieRepository->findAll();

        return $this->render('pages/categorie_show.html.twig', [
            'categorie' => $categorie,
            'produits' => $products,
            'categories' => $categories,  // <-- IMPORTANT pour Twig
        ]);
    }
}
