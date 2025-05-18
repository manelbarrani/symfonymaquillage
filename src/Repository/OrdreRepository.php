<?php
namespace App\Repository;

use App\Entity\Ordre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrdreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ordre::class);
    }

    // Ajoutez vos méthodes personnalisées ici si besoin
}
