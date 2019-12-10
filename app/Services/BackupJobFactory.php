<?php

namespace App\Services;

use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Tasks\Backup\BackupJob;
use Spatie\Backup\Tasks\Backup\BackupJobFactory as SpatieBackupJobFactory;

class BackupJobFactory extends SpatieBackupJobFactory
{
    public static function createFromArray(array $config): BackupJob
    {
        return (new BackupJob())
            ->setFileSelection(static::createFileSelection($config['backup']['source']['files']))
            ->setDbDumpers(
                static::createDbDumpers($config['backup']['source']['databases'])
                ->merge(TenantBackupDbSelector::getTenantDatabaseConnections())
            )
            ->setBackupDestinations(BackupDestinationFactory::createFromArray($config['backup']));
    }
}
