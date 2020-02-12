<?php

namespace App\Repository;

use App\Entity\Monuments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method Monuments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Monuments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Monuments[]    findAll()
 * @method Monuments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MonumentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Monuments::class);
    }

    public function findAllMonuments(): JsonResponse
    {
        $response = array();
        $results = $this->findAll();

        if (!$results) {
            return new JsonResponse(['message' => 'The response does not contain any data.'], Response::HTTP_NOT_FOUND);
        }

        foreach ($results as $result) {
            $response[] = array(
                'id' => $result->getId(),
                'name' => $result->getName(),
                'longitude' => $result->getLongitude(),
                'latitude' => $result->getLatitude(),
                'address' => $result->getAddress(),
                'city' => $result->getCity(),
                'zipcode' => $result->getZipcode(),
                'sport' => $result->getSport(),
                'img_url' => $result->getImgUrl(),
            );
        }
        return new JsonResponse($response);
    }

    public function findOneMonument(int $id): JsonResponse
    {
        $results = $this->createQueryBuilder('m')
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;

        if (!$results) {
            return new JsonResponse(['message' => 'This id does not match any monument'], Response::HTTP_NOT_FOUND);
        }

        foreach ($results as $result) {
            $response = array(
                'id' => $result->getId(),
                'name' => $result->getName(),
                'longitude' => $result->getLongitude(),
                'latitude' => $result->getLatitude(),
                'address' => $result->getAddress(),
                'city' => $result->getCity(),
                'zipcode' => $result->getZipcode(),
                'sport' => $result->getSport(),
                'img_url' => $result->getImgUrl(),
            );
        }
        return new JsonResponse($response);
    }

    public function findAllMonumentsNoJsoned()
    {
        $response = array();
        $results = $this->findAll();

        if (!$results) {
            return new JsonResponse(['message' => 'The response does not contain any data.'], Response::HTTP_NOT_FOUND);
        }

        foreach ($results as $result) {
            $response[] = array(
                'id' => $result->getId(),
                'name' => $result->getName(),
                'longitude' => $result->getLongitude(),
                'latitude' => $result->getLatitude(),
                'address' => $result->getAddress(),
                'city' => $result->getCity(),
                'zipcode' => $result->getZipcode(),
                'sport' => $result->getSport(),
                'img_url' => $result->getImgUrl(),
            );
        }
        return $response;
    }

    public function getCountOfInterestsByIdAndDist(int $id, int $dist): JsonResponse
    {
        $response = array();

        $conn = $this->getEntityManager()->getConnection();

        $sqlQueries = array(
            'trilibs' => 'SELECT COUNT(*) as trilib FROM monuments m INNER JOIN trilib_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :dist;',
            'electric_terms' => 'SELECT COUNT(*) as electric_term FROM monuments m INNER JOIN electricterminal_distance_monument e ON m.id = e.id_monuments WHERE m.id = :id AND e.distance_km < :dist;',
            'trimobiles' => 'SELECT COUNT(*) as trimobile FROM monuments m INNER JOIN trimobile_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :dist;',
            'velibs' => 'SELECT COUNT(*) as velib FROM monuments m INNER JOIN velib_distance_monument v ON m.id = v.id_monuments WHERE m.id = :id AND v.distance_km < :dist;',
        );

        foreach ($sqlQueries as $key => $query) {
            try {
                $stmt = $conn->prepare($query);
                $stmt->execute(['id' => $id, 'dist' => $dist]);
                $result = $stmt->fetchAll();
                array_push($response, $result);
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }

        if (!$response) {
            return new JsonResponse(['message' => 'The response does not contain any data.'], Response::HTTP_NOT_FOUND);
        }

        $flattenArray = array();
        $multiDimensionArray = array($response);

        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($multiDimensionArray));
        foreach ($iterator as $key => $value) {
            $flattenArray[$key] = (int) $value;
        }

        return new JsonResponse($flattenArray);
    }

    public function getCountOfInterestsByIdAndMultipleDist(int $id, int $distTrilibs, int $distElecs, int $distTrimobile, int $distVelib): JsonResponse
    {
        $response = array();

        $conn = $this->getEntityManager()->getConnection();

        $sqlQueries = array(
            'trilibs' => 'SELECT COUNT(*) as trilib FROM monuments m INNER JOIN trilib_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :distTrilibs;',
            'electric_terms' => 'SELECT COUNT(*) as electric_term FROM monuments m INNER JOIN electricterminal_distance_monument e ON m.id = e.id_monuments WHERE m.id = :id AND e.distance_km < :distElecs;',
            'trimobiles' => 'SELECT COUNT(*) as trimobile FROM monuments m INNER JOIN trimobile_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :distTrimobile;',
            'velibs' => 'SELECT COUNT(*) as velib FROM monuments m INNER JOIN velib_distance_monument v ON m.id = v.id_monuments WHERE m.id = :id AND v.distance_km < :distVelib;',
        );

        foreach ($sqlQueries as $key => $query) {
            try {
                $stmt = $conn->prepare($query);
                switch ($key) {
                    case 'trilibs':
                        $stmt->execute([
                            'id' => $id,
                            'distTrilibs' => $distTrilibs,
                        ]);
                        break;
                    case 'electric_terms':
                        $stmt->execute([
                            'id' => $id,
                            'distElecs' => $distElecs,
                        ]);
                        break;
                    case 'trimobiles':
                        $stmt->execute([
                            'id' => $id,
                            'distTrimobile' => $distTrimobile,
                        ]);
                        break;
                    case 'velibs':
                        $stmt->execute([
                            'id' => $id,
                            'distVelib' => $distVelib,
                        ]);
                        break;
                }

                $result = $stmt->fetchAll();
                array_push($response, $result);
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }

        if (!$response) {
            return new JsonResponse(['message' => 'The response does not contain any data.'], Response::HTTP_NOT_FOUND);
        }

        $flattenArray = array();
        $multiDimensionArray = array($response);

        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($multiDimensionArray));
        foreach ($iterator as $key => $value) {
            $flattenArray[$key] = (int) $value;
        }

        return new JsonResponse($flattenArray);
    }

    public function getInterestsByIdAndDist(int $id, int $dist): JsonResponse
    {
        $response = array();

        $conn = $this->getEntityManager()->getConnection();

        $sqlQueries = array(
            'trilibs' => 'SELECT t.id, t.distance_km, t.id_trilib, t.id_monuments FROM monuments m INNER JOIN trilib_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :dist ORDER BY t.distance_km;',
            'electric_terms' => 'SELECT e.id, e.distance_km, e.id_electricterminal, e.id_monuments FROM monuments m INNER JOIN electricterminal_distance_monument e ON m.id = e.id_monuments WHERE m.id = :id AND e.distance_km < :dist ORDER BY e.distance_km;',
            'trimobiles' => 'SELECT t.id, t.distance_km, t.id_trimobile, t.id_monuments FROM monuments m INNER JOIN trimobile_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :dist ORDER BY t.distance_km;',
            'velibs' => 'SELECT v.id, v.distance_km, v.id_velib, v.id_monuments FROM monuments m INNER JOIN velib_distance_monument v ON m.id = v.id_monuments WHERE m.id = :id AND v.distance_km < :dist ORDER BY v.distance_km;',
        );

        foreach ($sqlQueries as $key => $query) {
            try {
                $stmt = $conn->prepare($query);
                $stmt->execute(['id' => $id, 'dist' => $dist]);
                $result = $stmt->fetchAll();
                $response[$key] = $result;
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }

        if (!$response) {
            return new JsonResponse(['message' => 'The response does not contain any data.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($response);
    }

    public function getInterestsByIdAndMultipleDist(int $id, int $distTrilibs, int $distElecs, int $distTrimobile, int $distVelib): JsonResponse
    {
        $response = array();

        $conn = $this->getEntityManager()->getConnection();

        $sqlQueries = array(
            'trilibs' => 'SELECT t.id, t.distance_km, t.id_trilib, t.id_monuments FROM monuments m INNER JOIN trilib_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :distTrilibs ORDER BY t.distance_km;',
            'electric_terms' => 'SELECT e.id, e.distance_km, e.id_electricterminal, e.id_monuments FROM monuments m INNER JOIN electricterminal_distance_monument e ON m.id = e.id_monuments WHERE m.id = :id AND e.distance_km < :distElecs ORDER BY e.distance_km;',
            'trimobiles' => 'SELECT t.id, t.distance_km, t.id_trimobile, t.id_monuments FROM monuments m INNER JOIN trimobile_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :distTrimobile ORDER BY t.distance_km;',
            'velibs' => 'SELECT v.id, v.distance_km, v.id_velib, v.id_monuments FROM monuments m INNER JOIN velib_distance_monument v ON m.id = v.id_monuments WHERE m.id = :id AND v.distance_km < :distVelib ORDER BY v.distance_km;',
        );

        foreach ($sqlQueries as $key => $query) {
            try {
                $stmt = $conn->prepare($query);
                switch ($key) {
                    case 'trilibs':
                        $stmt->execute([
                            'id' => $id,
                            'distTrilibs' => $distTrilibs,
                        ]);
                        break;
                    case 'electric_terms':
                        $stmt->execute([
                            'id' => $id,
                            'distElecs' => $distElecs,
                        ]);
                        break;
                    case 'trimobiles':
                        $stmt->execute([
                            'id' => $id,
                            'distTrimobile' => $distTrimobile,
                        ]);
                        break;
                    case 'velibs':
                        $stmt->execute([
                            'id' => $id,
                            'distVelib' => $distVelib,
                        ]);
                        break;
                }
                $result = $stmt->fetchAll();
                $response[$key] = $result;
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }

        if (!$response) {
            return new JsonResponse(['message' => 'The response does not contain any data.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($response);
    }

    public function findAllMonumentsAndTheirInterests(int $dist): JsonResponse
    {
        $response = array();
        $monuments = $this->findAll();

        $sqlQueries = array(
            'trilibs' => 'SELECT t.id_trilib FROM monuments m INNER JOIN trilib_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :dist ORDER BY t.distance_km;',
            'electric_terms' => 'SELECT e.id_electricterminal FROM monuments m INNER JOIN electricterminal_distance_monument e ON m.id = e.id_monuments WHERE m.id = :id AND e.distance_km < :dist ORDER BY e.distance_km;',
            'trimobiles' => 'SELECT t.id_trimobile FROM monuments m INNER JOIN trimobile_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :dist ORDER BY t.distance_km;',
            'velibs' => 'SELECT v.id_velib FROM monuments m INNER JOIN velib_distance_monument v ON m.id = v.id_monuments WHERE m.id = :id AND v.distance_km < :dist ORDER BY v.distance_km;',
        );

        $conn = $this->getEntityManager()->getConnection();

        foreach ($monuments as $index => $monument) {
            $monumentId = $monument->getId();

            $response[] = array(
                'id' => $monument->getId(),
                'name' => $monument->getName(),
                'longitude' => $monument->getLongitude(),
                'latitude' => $monument->getLatitude(),
                'address' => $monument->getAddress(),
                'city' => $monument->getCity(),
                'zipcode' => $monument->getZipcode(),
                'sport' => $monument->getSport(),
                'img_url' => $monument->getImgUrl(),
                'interests' => array(),
            );

            foreach ($sqlQueries as $innerKey => $query) {
                try {
                    $stmt = $conn->prepare($query);
                    $stmt->execute(['id' => $monumentId, 'dist' => $dist]);
                    $result = $stmt->fetchAll();
                    $response[$index]['interests'][$innerKey] = array();

                    switch ($innerKey) {
                        case 'trilibs':
                            $nameProp = 'id_trilib';
                            break;
                        case 'electric_terms':
                            $nameProp = 'id_electricterminal';
                            break;
                        case 'trimobiles':
                            $nameProp = 'id_trimobile';
                            break;
                        case 'velibs':
                            $nameProp = 'id_velib';
                            break;
                    }

                    foreach ($result as $key => $id) {
                        $idItem = $id[$nameProp];
                        array_push($response[$index]['interests'][$innerKey], (int) $idItem);
                    }
                } catch (\Throwable $e) {
                    echo $e->getMessage();
                }
            }
        }

        return new JsonResponse($response);
    }

    public function findAllMonumentsAndTheirInterestsMultipleDist(int $trilibDistParam, int  $elecsDistParam, int  $trimobileDistParam, int  $velibDistParam): JsonResponse
    {
        $response = array();
        $monuments = $this->findAll();

        $sqlQueries = array(
            'trilibs' => 'SELECT t.id_trilib FROM monuments m INNER JOIN trilib_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :trilibDistParam ORDER BY t.distance_km;',
            'electric_terms' => 'SELECT e.id_electricterminal FROM monuments m INNER JOIN electricterminal_distance_monument e ON m.id = e.id_monuments WHERE m.id = :id AND e.distance_km < :elecsDistParam ORDER BY e.distance_km;',
            'trimobiles' => 'SELECT t.id_trimobile FROM monuments m INNER JOIN trimobile_distance_monument t ON m.id = t.id_monuments WHERE m.id = :id AND t.distance_km < :trimobileDistParam ORDER BY t.distance_km;',
            'velibs' => 'SELECT v.id_velib FROM monuments m INNER JOIN velib_distance_monument v ON m.id = v.id_monuments WHERE m.id = :id AND v.distance_km < :velibDistParam ORDER BY v.distance_km;',
        );

        $conn = $this->getEntityManager()->getConnection();

        foreach ($monuments as $index => $monument) {
            $monumentId = $monument->getId();

            $response[] = array(
                'id' => $monument->getId(),
                'name' => $monument->getName(),
                'longitude' => $monument->getLongitude(),
                'latitude' => $monument->getLatitude(),
                'address' => $monument->getAddress(),
                'city' => $monument->getCity(),
                'zipcode' => $monument->getZipcode(),
                'sport' => $monument->getSport(),
                'img_url' => $monument->getImgUrl(),
                'interests' => array(),
            );

            foreach ($sqlQueries as $innerKey => $query) {
                try {
                    $stmt = $conn->prepare($query);
                    switch ($innerKey) {
                        case 'trilibs':
                            $stmt->execute([
                                'id' => $monumentId,
                                'trilibDistParam' => $trilibDistParam,
                            ]);
                            break;
                        case 'electric_terms':
                            $stmt->execute([
                                'id' => $monumentId,
                                'elecsDistParam' => $elecsDistParam,
                            ]);
                            break;
                        case 'trimobiles':
                            $stmt->execute([
                                'id' => $monumentId,
                                'trimobileDistParam' => $trimobileDistParam,
                            ]);
                            break;
                        case 'velibs':
                            $stmt->execute([
                                'id' => $monumentId,
                                'velibDistParam' => $velibDistParam,
                            ]);
                            break;
                    }
                    $result = $stmt->fetchAll();
                    $response[$index]['interests'][$innerKey] = array();

                    switch ($innerKey) {
                        case 'trilibs':
                            $nameProp = 'id_trilib';
                            break;
                        case 'electric_terms':
                            $nameProp = 'id_electricterminal';
                            break;
                        case 'trimobiles':
                            $nameProp = 'id_trimobile';
                            break;
                        case 'velibs':
                            $nameProp = 'id_velib';
                            break;
                    }

                    foreach ($result as $key => $id) {
                        $idItem = $id[$nameProp];
                        array_push($response[$index]['interests'][$innerKey], (int) $idItem);
                    }
                } catch (\Throwable $e) {
                    echo $e->getMessage();
                }
            }
        }

        return new JsonResponse($response);
    }

    // /**
    //  * @return Monuments[] Returns an array of Monuments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Monuments
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
