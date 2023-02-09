<?php

namespace App\Repository;

use App\Entity\User;
use App\Form\Backoffice\User\SearchUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface

{
//    public PaginationInterface $paginationInterface;
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, User::class);
//    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }


    public function getUserByValidToken(string $token): ?User
    {
        $now = (new \DateTime())->modify('-1 days');
        return $this->createQueryBuilder('user')
            ->where('user.resetPasswordToken = :token')
            ->andWhere('user.resetPasswordRequestAt >= :now')
            ->setParameters(['token' => $token, 'now' => $now])
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    public function findAllExcept($id)
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.id != :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
//    /** @param SearchUsers $searchUsers
//     *@return PaginationInterface
//     *
//     */

//    public function findBySearch(SearchUsers $searchUsers): PaginationInterface{
//       $data = $this->createQueryBuilder('p')
//           ->where('p.firstName LIKE :firstName')
//           ->setParameter('firstName', '%firstname%');
//
//       if(!empty($searchUsers->q)){
//           $data = $data
//           ->andWhere('p.firstName LIKE :q')
//           ->setParameter('q', "%{$searchUsers->q}%");
//       }
//
//       return $data->getQuery()->getResult();
//       //$users = $this->paginationInterface->paginate($data, $searchUsers->page, 9 );
//       //return $users;
//    }

//$dql = "SELECT p, c FROM BlogPost p JOIN p.comments c";
//$query = $entityManager->createQuery($dql)
//->setFirstResult(0)
//->setMaxResults(100);
//
//$paginator = new Paginator($query, $fetchJoinCollection = true);
//
//$c = count($paginator);
//foreach ($paginator as $post) {
//echo $post->getHeadline() . "\n";




}
