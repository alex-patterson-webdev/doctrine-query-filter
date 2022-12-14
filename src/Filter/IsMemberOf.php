<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query\Expr;

final class IsMemberOf extends AbstractExpression
{
    protected function createExpression(Expr $expr, string $fieldName, string $parameterName, string $alias): string
    {
        return (string)$expr->isMemberOf(':' . $parameterName, $alias . '.' . $fieldName);
    }

    /**
     * @param MetadataInterface $metadata
     * @param array<mixed> $criteria
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws MetadataException
     */
    protected function resolveFieldName(MetadataInterface $metadata, array $criteria): string
    {
        $fieldName = parent::resolveFieldName($metadata, $criteria);

        if ($metadata->hasAssociation($fieldName)) {
            $associationType = $metadata->getAssociationMapping($fieldName)['type'] ?? '';

            if (!empty($associationType) && !($associationType & ClassMetadataInfo::TO_ONE)) {
                return $fieldName;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Unable to apply query filter \'%s\': '
                    . 'The field \'%s\' is not a collection valued association',
                    self::class,
                    $fieldName
                )
            );
        }

        throw new InvalidArgumentException(
            sprintf(
                'Unable to apply query filter \'%s\': '
                . 'The entity class \'%s\' has no association named \'%s\'',
                self::class,
                $metadata->getName(),
                $fieldName
            )
        );
    }
}
