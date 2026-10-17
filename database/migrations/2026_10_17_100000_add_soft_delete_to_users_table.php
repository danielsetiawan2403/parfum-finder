<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // deleted_at: akun "dihapus" tetap ada di database (tidak benar-benar hilang),
            // sehingga riwayat pembayarannya aman untuk laporan pendapatan.
            $table->softDeletes();

            // Alasan penghapusan dari admin (dikirim ke email dan tampil saat user mencoba login).
            $table->text('deletion_reason')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('deletion_reason');
            $table->dropSoftDeletes();
        });
    }
};
