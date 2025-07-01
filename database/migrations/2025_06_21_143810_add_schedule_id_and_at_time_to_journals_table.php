    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('journals', function (Blueprint $table) {
                // Tambahkan schedule_id jika belum ada
                if (!Schema::hasColumn('journals', 'schedule_id')) {
                    $table->foreignId('schedule_id')
                          ->nullable() // Atau remove nullable jika setiap jurnal HARUS punya jadwal
                          ->constrained('schedules')
                          ->onDelete('set null')
                          ->after('user_id'); // Posisikan setelah user_id
                }

                // Tambahkan at_time jika belum ada
                if (!Schema::hasColumn('journals', 'at_time')) {
                    $table->string('at_time')->nullable()->after('end_time'); // Posisikan setelah end_time
                }
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('journals', function (Blueprint $table) {
                if (Schema::hasColumn('journals', 'schedule_id')) {
                    $table->dropConstrainedForeignId('schedule_id');
                }
                if (Schema::hasColumn('journals', 'at_time')) {
                    $table->dropColumn('at_time');
                }
            });
        }
    };
    