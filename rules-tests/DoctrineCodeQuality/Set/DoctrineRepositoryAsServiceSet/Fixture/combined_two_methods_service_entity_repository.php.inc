<?php

declare(strict_types=1);

namespace Rector\Tests\DoctrineCodeQuality\Set\DoctrineRepositoryAsServiceSet\Fixture;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Rector\Tests\DoctrineCodeQuality\Set\DoctrineRepositoryAsServiceSet\Source\Car;

final class CombinedTwomethodsServiceEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    public function deleteByEnabled(): void
    {
        foreach ($this->findByEnabled(false) as $inactiveUser) {
            $this->_em->remove($inactiveUser);
        }
        $this->_em->flush();
    }

    /** @return Car[] */
    public function findByEnabled(bool $enabled): array
    {
        return $this->findBy([
            'enabled' => $enabled
        ]);
    }
}

?>
-----
<?php

declare(strict_types=1);

namespace Rector\Tests\DoctrineCodeQuality\Set\DoctrineRepositoryAsServiceSet\Fixture;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Rector\Tests\DoctrineCodeQuality\Set\DoctrineRepositoryAsServiceSet\Source\Car;

final class CombinedTwomethodsServiceEntityRepository
{
    /**
     * @var \Doctrine\ORM\EntityRepository<\Rector\Tests\DoctrineCodeQuality\Set\DoctrineRepositoryAsServiceSet\Source\Car>
     */
    private \Doctrine\ORM\EntityRepository $repository;
    public function __construct(private \Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(\Rector\Tests\DoctrineCodeQuality\Set\DoctrineRepositoryAsServiceSet\Source\Car::class);
    }

    public function deleteByEnabled(): void
    {
        foreach ($this->findByEnabled(false) as $inactiveUser) {
            $this->entityManager->remove($inactiveUser);
        }
        $this->entityManager->flush();
    }

    /** @return Car[] */
    public function findByEnabled(bool $enabled): array
    {
        return $this->repository->findBy([
            'enabled' => $enabled
        ]);
    }
}

?>
