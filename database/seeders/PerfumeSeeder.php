<?php

namespace Database\Seeders;

use App\Models\Perfume;
use Illuminate\Database\Seeder;

class PerfumeSeeder extends Seeder
{
    /**
     * Foto disimpan di public/images/parfum/ (tulis nama file lengkap dengan ekstensinya).
     * Kategori, harga, notes, dan deskripsi bisa dilengkapi lewat HeidiSQL/phpMyAdmin
     * atau diedit di file ini lalu seeder dijalankan ulang.
     */
    public function run(): void
    {
        // 3 parfum dengan data lengkap (data contoh, silakan sesuaikan).
        $detailed = [
            [
                'name'         => 'Versace Eros',
                'category'     => 'Pria',
                'price'        => 1500000,
                'image'        => 'images/parfum/eros.jpg',
                'top_notes'    => 'Mint, Green Apple, Lemon.',
                'middle_notes' => 'Tonka Bean, Ambroxan, Geranium.',
                'base_notes'   => 'Vanilla, Vetiver, Oakmoss, Cedarwood.',
                'description'  => 'Versace Eros adalah wewangian pria yang segar sekaligus manis, dengan kesan energik dan percaya diri. Cocok untuk acara malam maupun pemakaian santai.',
            ],
            [
                'name'         => 'YSL Y EDP',
                'category'     => 'Pria',
                'price'        => 2400000,
                'image'        => 'images/parfum/ysl.png',
                'top_notes'    => 'Bergamot, Ginger, Apple.',
                'middle_notes' => 'Sage, Juniper Berries, Geranium.',
                'base_notes'   => 'Vetiver, Cedarwood, Amberwood, Tonka Bean, Olibanum.',
                'description'  => 'YSL Y Eau de Parfum (EDP) adalah wewangian pria yang intens, modern, dan maskulin, menampilkan perpaduan fresh (apel, bergamot) dan woody (sage, vetiver, amberwood) yang tahan lama. Dirancang untuk pria yang berani dan sukses, aroma ini memberikan kesan bersih, berani, dan berenergi, cocok untuk penggunaan sehari-hari maupun acara khusus.',
            ],
            [
                'name'         => 'MFK Grand Soir',
                'category'     => 'Unisex',
                'price'        => 4500000,
                'image'        => 'images/parfum/mfk-grand-soir.png',
                'top_notes'    => 'Labdanum, Benzoin.',
                'middle_notes' => 'Amber, Tonka Bean.',
                'base_notes'   => 'Vanilla, Amberwood.',
                'description'  => 'Maison Francis Kurkdjian Grand Soir adalah wewangian amber yang hangat, manis, dan elegan. Kesan mewahnya cocok untuk acara malam dan momen spesial.',
            ],
        ];

        foreach ($detailed as $perfume) {
            Perfume::updateOrCreate(['name' => $perfume['name']], $perfume);
        }

        // Parfum lain: nama, kategori (perkiraan, silakan koreksi), dan nama file foto lengkap.
        $basic = [
            ['MFK 540 Baccarat Rouge',          'Unisex', 'mkf-540.png'],
            ['PDM Althair',                      'Pria',   'pdm.png'],
            ['PDM Delina',                       'Wanita', 'pdm-delina.png'],
            ['JPG Le Male Elixir',               'Pria',   'jpg-elix.png'],
            ['JPG Le Male Le Parfum',            'Pria',   'jpg-lf.png'],
            ['LV Symphony',                      'Unisex', 'lvs.png'],
            ['LV Imagination',                   'Pria',   'lv.png'],
            ['Bleu De Chanel',                   'Pria',   'bdc.jpg'],
            ['YSL Black Opium',                  'Wanita', 'bo.jpg'],
            ['Chanel No.5',                      'Wanita', 'cn5.jpg'],
            ['Jo Malone English Pear & Freesia', 'Unisex', 'jme.jpg'],
            ['Dior Sauvage',                     'Pria',   'dior-sauvage.jpg'],
        ];

        foreach ($basic as [$name, $category, $file]) {
            Perfume::updateOrCreate(
                ['name' => $name],
                ['category' => $category, 'image' => "images/parfum/{$file}"]
            );
        }
    }
}