<?php


namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\OrX;
use Doctrine\ORM\EntityManager;

class Test
{

    public function test()
    {
        /** @var EntityManager $entityManager */
        $entityManager = 'foo';

        $factory = new QueryFilterFactory(new QueryFilterManager());

        $queryBuilder = new QueryBuilder(
            $entityManager->createQueryBuilder(),
            $factory
        );

        $queryBuilder->select('u')
            ->from('Users', 'u')
            ->where([
                [
                    'type'  => IsDeleted::class,
                    'field' => ''
                ],
                [
                    'type'   => OrX::class,
                    'params' => [

                    ]
                ]
            ]);
    }

}