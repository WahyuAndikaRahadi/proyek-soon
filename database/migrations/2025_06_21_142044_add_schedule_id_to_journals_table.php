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
                // Tambahkan kolom schedule_id sebagai foreign key
                $table->foreignId('schedule_id')
                      ->nullable() // Atau remove nullable jika setiap jurnal HARUS punya jadwal
                      ->constrained('schedules')
                      ->onDelete('set null'); // Atau 'cascade' atau 'restrict' sesuai kebutuhan

                // Opsional: Jika Anda ingin mempertahankan class_id dan subject_id
                // dan mereka juga merupakan foreign keys ke tabel classes dan subjects,
                // pastikan mereka juga didefinisikan sebagai foreign keys.
                // Jika jurnal selalu terkait dengan jadwal, maka class_id dan subject_id
                // bisa secara logis didapatkan dari relasi schedule.
                // Namun, untuk fleksibilitas, seringkali disimpan juga di jurnal.
                // Misal: $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
                // Misal: $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
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
                // Hapus foreign key constraint sebelum menghapus kolom
                $table->dropConstrainedForeignId('schedule_id');
                // $table->dropForeign(['schedule_id']); // Alternatif jika dropConstrainedForeignId tidak bekerja pada versi lama
                $table->dropColumn('schedule_id');
            });
        }
    };
    