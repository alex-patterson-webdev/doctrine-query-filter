<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\Exception\InvalidOptionException;
use Arp\Stdlib\Service\OptionsAwareInterface;
use Arp\Stdlib\Service\OptionsAwareTrait;

/**
 * AbstractQueryFilter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
abstract class AbstractQueryFilter implements QueryFilterInterface, MetadataAwareInterface, OptionsAwareInterface
{
    /**
     * @trait OptionsAwareTrait
     * @trait MetadataAwareTrait
     */
    use OptionsAwareTrait,
        MetadataAwareTrait;

    /**
     * __construct.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * typeCast
     *
     * @param string $fieldName
     * @param mixed  $value
     * @param string $format
     *
     * @return mixed
     *
     * @throws InvalidOptionException
     */
    protected function typeCast($fieldName, $value, $format)
    {
        if (! $this->hasMetadata()) {
            return $value;
        }

        $metadata   = $this->getMetadata();
        $entityName = $metadata->getName();

        if (! $metadata->hasField($fieldName) || ! $metadata->hasAssociation($fieldName)) {
            throw InvalidOptionException::invalidFieldNameException($fieldName, $entityName);
        }

        return $value;
    }

}