<?php

namespace App;

use Illuminate\Support\Facades\Date;
use Illuminatech\Config\StorageDb;
use Spatie\Activitylog\ActivityLogger;

class Settings extends StorageDb
{
    /**
     * Using the Settings table instead of the default.
     */
    public $table = 'settings';

    public $filter = [];

    /**
     * {@inheritDoc}
     *
     * This overload method adds Timestamps and ActivityLogger
     */
    public function save(array $values): bool
    {
        $existingValues = $this->get();

        foreach ($values as $key => $value) {
            if (array_key_exists($key, $existingValues)) {
                if ($value === $existingValues[$key]) {
                    continue;
                }

                $this->connection->table($this->table)
                    ->where($this->filter)
                    ->where([$this->keyColumn => $key])
                    ->update([$this->valueColumn => $value, 'updated_at' => Date::now()]);

                $logger = app(ActivityLogger::class)->withProperties(['attributes'=> [$key => $value], 'old' => [$key => $existingValues[$key]]]);
                $logger->log('Updated Setting: ' . $key);

            } else {
                $this->connection->table($this->table)
                    ->insert(array_merge(
                        $this->filter,
                        [
                            $this->keyColumn => $key,
                            $this->valueColumn => $value,
                            'created_at' => Date::now(),
                            'updated_at' => Date::now(),
                        ]
                    ));

                $logger = app(ActivityLogger::class)->withProperties(['attributes' => [$key => $value]]);
                $logger->log('Added Setting: ' . $key);
            }
        }

        return true;
    }

}
