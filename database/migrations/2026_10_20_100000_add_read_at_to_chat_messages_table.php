<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Kapan pesan dibaca penerimanya. NULL = belum dibaca.
            // Dipakai untuk titik merah di lonceng notifikasi user (pesan dari admin).
            $table->timestamp('read_at')->nullable()->after('body');
        });

        // Pesan lama dianggap sudah dibaca, supaya tidak muncul titik merah palsu setelah migrasi.
        DB::table('chat_messages')->whereNull('read_at')->update(['read_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }
};
