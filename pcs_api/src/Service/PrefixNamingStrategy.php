<?php

namespace App\Service;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;


class PrefixNamingStrategy extends UnderscoreNamingStrategy
{
    private $prefix = 'SCP';

    public function __construct()
    {

        parent::__construct(CASE_LOWER);
    }
    public function joinKeyJoinColumnName($entityName, $referencedColumnName = null)
    {
        return $this->prefix . '_' . $entityName . '_' . ($referencedColumnName ?: 'id');
    }

    public function classToTableName(string $className): string
    {
        return $this->prefix . '_' . parent::classToTableName($className);
    }
}
