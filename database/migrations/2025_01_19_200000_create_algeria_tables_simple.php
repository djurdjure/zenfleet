<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Simple Algeria tables creation without complex modifications
 * Focus on creating the essential Algeria lookup tables first
 */
return new class extends Migration
{
    private array $algerianWilayas = [
        '01' => 'Adrar',
        '02' => 'Chlef',
        '03' => 'Laghouat',
        '04' => 'Oum El Bouaghi',
        '05' => 'Batna',
        '06' => 'Béjaïa',
        '07' => 'Biskra',
        '08' => 'Béchar',
        '09' => 'Blida',
        '10' => 'Bouira',
        '11' => 'Tamanrasset',
        '12' => 'Tébessa',
        '13' => 'Tlemcen',
        '14' => 'Tiaret',
        '15' => 'Tizi Ouzou',
        '16' => 'Alger',
        '17' => 'Djelfa',
        '18' => 'Jijel',
        '19' => 'Sétif',
        '20' => 'Saïda',
        '21' => 'Skikda',
        '22' => 'Sidi Bel Abbès',
        '23' => 'Annaba',
        '24' => 'Guelma',
        '25' => 'Constantine',
        '26' => 'Médéa',
        '27' => 'Mostaganem',
        '28' => 'M\'Sila',
        '29' => 'Mascara',
        '30' => 'Ouargla',
        '31' => 'Oran',
        '32' => 'El Bayadh',
        '33' => 'Illizi',
        '34' => 'Bordj Bou Arréridj',
        '35' => 'Boumerdès',
        '36' => 'El Tarf',
        '37' => 'Tindouf',
        '38' => 'Tissemsilt',
        '39' => 'El Oued',
        '40' => 'Khenchela',
        '41' => 'Souk Ahras',
        '42' => 'Tipaza',
        '43' => 'Mila',
        '44' => 'Aïn Defla',
        '45' => 'Naâma',
        '46' => 'Aïn Témouchent',
        '47' => 'Ghardaïa',
        '48' => 'Relizane'
    ];

    public function up(): void
    {
        $this->logMessage('Creating Algeria lookup tables');

        // Create Algeria wilayas table
        if (!Schema::hasTable('algeria_wilayas')) {
            Schema::create('algeria_wilayas', function (Blueprint $table) {
                $table->string('code', 2)->primary();
                $table->string('name_ar')->nullable();
                $table->string('name_fr');
                $table->string('name_en')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            $this->logMessage('Algeria wilayas table created');
        }

        // Create Algeria communes table
        if (!Schema::hasTable('algeria_communes')) {
            Schema::create('algeria_communes', function (Blueprint $table) {
                $table->id();
                $table->string('wilaya_code', 2);
                $table->string('name_ar')->nullable();
                $table->string('name_fr');
                $table->string('postal_code', 5)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('wilaya_code')->references('code')->on('algeria_wilayas');
                $table->index(['wilaya_code', 'name_fr']);
            });

            $this->logMessage('Algeria communes table created');
        }

        // Insert wilaya data
        foreach ($this->algerianWilayas as $code => $nameFr) {
            DB::table('algeria_wilayas')->insertOrIgnore([
                'code' => $code,
                'name_fr' => $nameFr,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Insert sample communes for major wilayas
        $sampleCommunes = [
            ['wilaya_code' => '16', 'name_fr' => 'Alger-Centre', 'postal_code' => '16000'],
            ['wilaya_code' => '16', 'name_fr' => 'Bab El Oued', 'postal_code' => '16020'],
            ['wilaya_code' => '31', 'name_fr' => 'Oran', 'postal_code' => '31000'],
            ['wilaya_code' => '25', 'name_fr' => 'Constantine', 'postal_code' => '25000'],
            ['wilaya_code' => '19', 'name_fr' => 'Sétif', 'postal_code' => '19000'],
            ['wilaya_code' => '09', 'name_fr' => 'Blida', 'postal_code' => '09000'],
            ['wilaya_code' => '05', 'name_fr' => 'Batna', 'postal_code' => '05000'],
            ['wilaya_code' => '06', 'name_fr' => 'Béjaïa', 'postal_code' => '06000'],
            ['wilaya_code' => '13', 'name_fr' => 'Tlemcen', 'postal_code' => '13000'],
            ['wilaya_code' => '23', 'name_fr' => 'Annaba', 'postal_code' => '23000'],
        ];

        foreach ($sampleCommunes as $commune) {
            DB::table('algeria_communes')->insertOrIgnore([
                ...$commune,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Add commune column to organizations if it doesn't exist
        if (!Schema::hasColumn('organizations', 'commune')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->string('commune')->nullable()->after('city');
            });
            $this->logMessage('Added commune column to organizations table');
        }

        $this->logMessage('Algeria tables setup completed successfully');
    }

    public function down(): void
    {
        Schema::dropIfExists('algeria_communes');
        Schema::dropIfExists('algeria_wilayas');

        if (Schema::hasColumn('organizations', 'commune')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->dropColumn('commune');
            });
        }

        $this->logMessage('Algeria tables removed');
    }

    private function logMessage(string $message): void
    {
        if (app()->runningInConsole()) {
            echo "[INFO] {$message}\n";
        }
    }
};