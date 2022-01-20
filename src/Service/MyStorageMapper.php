<?php

declare(strict_types=1);

namespace App\Service;

use DH\Auditor\Provider\Service\StorageServiceInterface;

class MyStorageMapper
{
    // the service expects 2 parameters and should return an object
    // implementing StorageServiceInterface
    public static function map(string $entity, array $storageServices): StorageServiceInterface
    {
        return $storageServices['dh_auditor.provider.doctrine.storage_services.doctrine.orm.default_entity_manager'];
    }
}
