<?php

namespace App\Repository;

use App\Entity\TrilibDistanceMonument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method TrilibDistanceMonument|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrilibDistanceMonument|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrilibDistanceMonument[]    findAll()
 * @method TrilibDistanceMonument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrilibDistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrilibDistanceMonument::class);
    }

    public function findAllTrilibDist()
    {
        $response = array();
        $results = $this->findAll();

        if (!$results) {
            return new JsonResponse(['message' => 'The response does not contain any data.'], Response::HTTP_NOT_FOUND);
        }

        foreach ($results as $result) {
            $response[] = array(
                'id' => $result->getId(),
                'distance_m' => $result->getDistanceKm(),
                'id_trilib' => $result->getIdTrilib()->getId(),
                'id_monuments' => $result->getIdMonuments()->getId(),
            );
        }
        return new JsonResponse($response);
    }

    public function findTrilibDistByIdMonument(int $id_monument)
    {
        $response = array();
        $results = $this->findAll();

        if (!$results) {
            return new JsonResponse(['message' => 'The response does not contain any data.'], Response::HTTP_NOT_FOUND);
        }

        foreach ($results as $result) {
            if($result->getIdMonuments()->getId() === $id_monument) {
                $response[] = array(
                    'id' => $result->getId(),
                    'distance_m' => $result->getDistanceKm(),
                    'id_trilib' => $result->getIdTrilib()->getId(),
                    'id_monuments' => $result->getIdMonuments()->getId(),
                );
            }
        }
        return new JsonResponse($response);
    }

    public function findTrilibDistByIdMonumentAndDist(int $id_monument, $dist)
    {
        $response = array();
        $results = $this->findAll();

        if (!$results) {
            return new JsonResponse(['message' => 'The response does not contain any data.'], Response::HTTP_NOT_FOUND);
        }

        foreach ($results as $result) {
            if($result->getIdMonuments()->getId() === $id_monument and $result->getDistanceKm() <= $dist) {
                $response[] = array(
                    'id' => $result->getId(),
                    'distance_m' => $result->getDistanceKm(),
                    'id_trilib' => $result->getIdTrilib()->getId(),
                    'id_monuments' => $result->getIdMonuments()->getId(),
                );
            }
        }
        return new JsonResponse($response);
    }

    // /**
    //  * @return TrilibDistanceMonument[] Returns an array of TrilibDistanceMonument objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrilibDistanceMonument
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
