<?php

namespace App\Listeners;

use Illuminate\Support\Collection;
use Spatie\Backup\Events\BackupZipWasCreated;
use ZipArchive;

class EncryptBackupZip
{
    public function handle(BackupZipWasCreated $event)
    {
        $pwd = config('backup.backup.password');

        if (null === $pwd) {
            consoleOutput()->info('No encryption requested.');
            return false;
        }

        $zip = new ZipArchive;
        $zip->open($event->pathToZip);
        $zip->setPassword($pwd);

        Collection::times($zip->numFiles, function ($i) use ($zip) {
            $zip->setEncryptionIndex($i - 1, ZipArchive::EM_AES_256);
        });

        $zip->close();
        consoleOutput()->info('Zip file encrypted.');
    }
}
