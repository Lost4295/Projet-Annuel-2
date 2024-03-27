<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Driver-level statement execution result.
 */
class Result implements \Doctrine\DBAL\Driver\Result
{
    public function fetchNumeric()
    {
    }

    public function fetchAssociative()
    {
    }

    public function fetchOne()
    {
    }

    public function fetchAllNumeric(): array
    {
    }

    public function fetchAllAssociative(): array
    {
    }


    public function fetchFirstColumn(): array
    {
    }


    public function rowCount(): int
    {
    }

    public function columnCount(): int
    {
    }
    public function free(): void
    {
    }
}
