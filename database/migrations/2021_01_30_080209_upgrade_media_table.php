<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        $conn = Schema::getConnection();
        $schema = $conn->getSchemaBuilder();
        $indexes = collect($schema->getIndexes('media'));

        $hasUuidUnique = $indexes->contains(function (array $index) {
            $name = $index['name'] ?? null;
            $columns = $index['columns'] ?? [];
            $unique = (bool) ($index['unique'] ?? false);

            return $name === 'media_uuid_unique' || ($unique && $columns === ['uuid']);
        });

        // Upgrade Spatie MediaLibrary schema from v8 to v9

        if (! $hasUuidUnique) {
            Schema::table('media', function (Blueprint $table) {
                $table->unique('uuid');
            });
        }

        if (! Schema::hasColumn('media', 'generated_conversions')) {
            Schema::table('media', function (Blueprint $table) {
                $table->json('generated_conversions');
            });
        }
    }
};
