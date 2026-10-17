<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Pendapatan Premium - {{ $periodLabel }}</title>
    <style>
        /* Laporan pendapatan: tampil rapi di layar dan saat dicetak (A4 portrait). */
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            font-size: 13px;
            line-height: 1.45;
            color: #0b1220;
            background: #e5e7eb;
        }

        /* ----- Bilah atas (hanya di layar, tidak ikut tercetak) ----- */
        .toolbar {
            position: sticky; top: 0; z-index: 5;
            display: flex; flex-wrap: wrap; align-items: center;
            padding: 10px 16px;
            background: #fff; border-bottom: 1px solid #cbd5e1;
        }
        .toolbar > * { margin: 4px 14px 4px 0; }
        .tb-back { color: #334155; text-decoration: none; font-weight: 600; }
        .tb-back:hover { text-decoration: underline; }
        .period { display: flex; flex-wrap: wrap; align-items: center; }
        .period label { margin-right: 10px; font-size: 12px; color: #475569; }
        .period input[type="date"] {
            margin-left: 4px; padding: 6px 8px; font: inherit; color: #0b1220;
            border: 1px solid #cbd5e1; border-radius: 8px; background: #fff;
        }
        .btn {
            display: inline-block; padding: 8px 14px; font: inherit; font-weight: 600;
            border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; color: #334155;
            cursor: pointer; text-decoration: none;
        }
        .btn:hover { background: #f1f5f9; }
        .btn-print { background: #2563eb; border-color: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .presets { font-size: 12px; color: #64748b; }
        .presets a {
            display: inline-block; margin-left: 6px; padding: 3px 10px;
            border: 1px solid #cbd5e1; border-radius: 999px;
            color: #334155; text-decoration: none; background: #fff;
        }
        .presets a:hover { background: #f1f5f9; }
        .presets a.is-active { background: #0b1220; border-color: #0b1220; color: #fff; }
        .tb-print { margin-left: auto; }

        /* ----- Kertas ----- */
        .sheet {
            max-width: 860px; margin: 18px auto 40px; padding: 30px 34px 34px;
            background: #fff; box-shadow: 0 6px 24px rgba(15, 23, 42, .12);
        }

        .doc-head { width: 100%; border-collapse: collapse; border-bottom: 3px solid #0b1220; margin-bottom: 18px; }
        .doc-head td { vertical-align: bottom; padding: 0 0 12px; }
        .brand { margin: 0 0 2px; font-size: 11px; font-weight: 700; letter-spacing: .14em; color: #475569; }
        .doc-head h1 { margin: 0; font-size: 22px; line-height: 1.2; }
        .meta { text-align: right; font-size: 12px; color: #334155; white-space: nowrap; }
        .meta div { margin-top: 2px; }
        .meta b { color: #0b1220; }

        h2 { margin: 22px 0 8px; font-size: 14px; letter-spacing: .02em; }

        /* ----- Ringkasan ----- */
        .stats { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 0 -6px; }
        .stats td { width: 20%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; vertical-align: top; }
        .stats td.main { width: 28%; background: #f1f5f9; border-color: #94a3b8; }
        .stats .k { display: block; font-size: 11px; color: #64748b; }
        .stats .v { display: block; margin-top: 2px; font-size: 15px; font-weight: 700; }
        .stats td.main .v { font-size: 19px; }
        .stats .s { display: block; margin-top: 2px; font-size: 10px; color: #64748b; }

        /* ----- Tabel ----- */
        table.data { width: 100%; border-collapse: collapse; }
        table.data th {
            padding: 7px 8px; background: #f1f5f9; border: 1px solid #cbd5e1;
            font-size: 11px; font-weight: 700; text-align: left; vertical-align: bottom;
        }
        table.data td { padding: 6px 8px; border: 1px solid #e2e8f0; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #fafbfd; }
        table.data .c { text-align: center; }
        table.data .r { text-align: right; white-space: nowrap; }
        table.data .nw { white-space: nowrap; }
        table.data .mono { font-family: Consolas, "Courier New", monospace; font-size: 11px; white-space: nowrap; }
        table.data tfoot td { background: #f1f5f9; border-color: #cbd5e1; font-weight: 700; }
        table.data thead { display: table-header-group; }
        table.data tr { page-break-inside: avoid; }
        .sub { display: block; font-size: 10.5px; color: #64748b; }
        .tag { display: inline-block; margin-left: 4px; padding: 0 6px; border: 1px solid #cbd5e1; border-radius: 999px; font-size: 10px; color: #64748b; }
        .empty { padding: 18px; border: 1px dashed #cbd5e1; border-radius: 8px; text-align: center; color: #64748b; }

        /* ----- Penutup ----- */
        .sign { width: 100%; margin-top: 26px; border-collapse: collapse; page-break-inside: avoid; }
        .sign td { vertical-align: top; padding: 0; }
        .note { font-size: 11px; color: #64748b; padding-right: 24px !important; }
        .sign-box { width: 190px; text-align: center; font-size: 12px; }
        .sign-space { height: 62px; }
        .sign-line { border-top: 1px solid #0b1220; padding-top: 3px; font-weight: 700; }

        /* ----- Layar kecil (HP): tabel bisa digeser ke samping. Khusus layar, tidak berlaku saat cetak. ----- */
        .scroll { overflow-x: auto; }
        @media screen and (max-width: 720px) {
            .sheet { margin: 0; padding: 20px 14px 28px; box-shadow: none; }
            .doc-head, .doc-head tbody, .doc-head tr, .doc-head td { display: block; width: 100%; }
            .meta { text-align: left; white-space: normal; }
            .stats, .stats tbody, .stats tr, .stats td { display: block; width: 100% !important; }
            .stats { border-spacing: 0; margin: 0; }
            .stats td { margin-bottom: 8px; }
            .tb-print { margin-left: 0; width: 100%; text-align: center; }
            table.data.wide { min-width: 640px; }
        }

        /* ----- Cetak ----- */
        @page { size: A4; margin: 14mm 12mm; }
        @media print {
            body { background: #fff; font-size: 11px; }
            .no-print { display: none !important; }
            .sheet { max-width: none; margin: 0; padding: 0; box-shadow: none; }
            .scroll { overflow: visible; }
            table.data.wide { min-width: 0; }
            .doc-head, .stats td.main, table.data th, table.data tfoot td, table.data tr:nth-child(even) td {
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            h2 { page-break-after: avoid; }
        }
    </style>
</head>
<body>

    {{-- Bilah atas: pilih periode dan cetak. Tidak ikut tercetak (class no-print). --}}
    <div class="toolbar no-print">
        <a href="{{ route('admin.premium.index') }}" class="tb-back">&larr; Kembali</a>

        <form action="{{ route('admin.premium.report') }}" method="GET" class="period">
            <label>Dari
                <input type="date" name="dari" value="{{ $from?->toDateString() }}">
            </label>
            <label>Sampai
                <input type="date" name="sampai" value="{{ $to?->toDateString() }}">
            </label>
            <button type="submit" class="btn">Terapkan</button>
        </form>

        {{-- Pintasan periode --}}
        <div class="presets">
            Pintasan:
            @foreach ($presets as $label => $params)
                @php
                    $active = ($from?->toDateString() === ($params['dari'] ?? null))
                           && ($to?->toDateString() === ($params['sampai'] ?? null));
                @endphp
                <a href="{{ route('admin.premium.report', $params) }}" @class(['is-active' => $active])>{{ $label }}</a>
            @endforeach
        </div>

        <button type="button" class="btn btn-print tb-print" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <main class="sheet">

        {{-- Kepala laporan --}}
        <table class="doc-head">
            <tr>
                <td>
                    <p class="brand">PARFUM FINDER</p>
                    <h1>Laporan Pendapatan Langganan Premium</h1>
                </td>
                <td class="meta">
                    <div>Periode: <b>{{ $periodLabel }}</b></div>
                    <div>Dicetak: {{ $printedAt->copy()->locale('id')->translatedFormat('j F Y, H:i') }} {{ $printedAt->format('T') }}</div>
                    <div>Dicetak oleh: {{ $printedBy }}</div>
                </td>
            </tr>
        </table>

        {{-- Ringkasan --}}
        <table class="stats">
            <tr>
                <td class="main">
                    <span class="k">Total Pendapatan</span>
                    <span class="v">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</span>
                </td>
                <td>
                    <span class="k">Jumlah Transaksi</span>
                    <span class="v">{{ number_format($summary['count'], 0, ',', '.') }}</span>
                </td>
                <td>
                    <span class="k">Member Berbayar</span>
                    <span class="v">{{ number_format($summary['members'], 0, ',', '.') }}</span>
                    <span class="s">orang (tanpa dihitung ganda)</span>
                </td>
                <td>
                    <span class="k">Rata-rata / Transaksi</span>
                    <span class="v">Rp {{ number_format($summary['average'], 0, ',', '.') }}</span>
                </td>
                <td>
                    <span class="k">Member Aktif</span>
                    <span class="v">{{ number_format($summary['activeNow'], 0, ',', '.') }}</span>
                    <span class="s">kondisi saat dicetak</span>
                </td>
            </tr>
        </table>

        @if ($transactions->isEmpty())
            <h2>Rincian Transaksi</h2>
            <div class="empty">Tidak ada transaksi pada periode ini.</div>
        @else
            {{-- Pendapatan per paket --}}
            <h2>Pendapatan per Paket</h2>
            <table class="data">
                <thead>
                    <tr>
                        <th>Paket</th>
                        <th class="r">Transaksi</th>
                        <th class="r">Pendapatan</th>
                        <th class="r">Porsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($byPlan as $plan)
                        <tr>
                            <td>{{ $plan->label }}</td>
                            <td class="r">{{ number_format($plan->count, 0, ',', '.') }}</td>
                            <td class="r">Rp {{ number_format($plan->revenue, 0, ',', '.') }}</td>
                            <td class="r">{{ number_format($summary['revenue'] > 0 ? $plan->revenue / $summary['revenue'] * 100 : 0, 1, ',', '.') }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pendapatan per bulan --}}
            <h2>Pendapatan per Bulan</h2>
            <table class="data">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th class="r">Transaksi</th>
                        <th class="r">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($byMonth as $month)
                        <tr>
                            <td>{{ $month->label }}</td>
                            <td class="r">{{ number_format($month->count, 0, ',', '.') }}</td>
                            <td class="r">Rp {{ number_format($month->revenue, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Rincian per transaksi --}}
            <h2>Rincian Transaksi</h2>
            <div class="scroll">
                <table class="data wide">
                    <thead>
                        <tr>
                            <th class="c">No</th>
                            <th>Tgl. Bayar</th>
                            <th>Invoice</th>
                            <th>Member</th>
                            <th>Paket</th>
                            <th class="r">Harga</th>
                            <th>Masa Aktif</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $t)
                            <tr>
                                <td class="c">{{ $loop->iteration }}</td>
                                <td class="nw">{{ $t->paid_at->copy()->locale('id')->translatedFormat('j M Y') }}<span class="sub">{{ $t->paid_at->format('H:i') }}</span></td>
                                <td class="mono">{{ $t->invoice }}</td>
                                <td>
                                    {{ $t->name }}
                                    @if ($t->deleted)<span class="tag">akun dihapus</span>@endif
                                    @if ($t->username)<span class="sub">{{ '@' . $t->username }}</span>@endif
                                </td>
                                <td>{{ $t->label }}</td>
                                <td class="r">Rp {{ number_format($t->price, 0, ',', '.') }}</td>
                                <td class="nw">
                                    {{ $t->starts_at?->copy()->locale('id')->translatedFormat('j M Y') }}
                                    <span class="sub">s/d {{ $t->ends_at?->copy()->locale('id')->translatedFormat('j M Y') }}</span>
                                </td>
                                <td>{{ $t->active ? 'Aktif' : 'Berakhir' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="r">Total Pendapatan ({{ number_format($summary['count'], 0, ',', '.') }} transaksi)</td>
                            <td class="r">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        {{-- Penutup --}}
        <table class="sign">
            <tr>
                <td class="note">
                    Catatan: laporan hanya menghitung langganan berstatus dibayar. Periode dihitung dari tanggal
                    pembayaran. Transaksi dari akun yang sudah dihapus tetap dihitung sebagai pendapatan.
                </td>
                <td class="sign-box">
                    Mengetahui,<br>Admin Parfum Finder
                    <div class="sign-space"></div>
                    <div class="sign-line">{{ $printedBy }}</div>
                </td>
            </tr>
        </table>
    </main>

</body>
</html>
