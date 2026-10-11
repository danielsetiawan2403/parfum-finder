<?php

namespace Database\Seeders;

use App\Models\Perfume;
use Illuminate\Database\Seeder;

class PerfumeSeeder extends Seeder
{
    /**
     * Gambar disimpan di public/images/parfum/ (nama file = slug + .png).
     * Kategori, harga, notes, dan deskripsi bisa dilengkapi lewat HeidiSQL
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
                'image'        => 'images/parfum/versace-eros.png',
                'top_notes'    => 'Mint, Green Apple, Lemon.',
                'middle_notes' => 'Tonka Bean, Ambroxan, Geranium.',
                'base_notes'   => 'Vanilla, Vetiver, Oakmoss, Cedarwood.',
                'description'  => 'Versace Eros adalah wewangian pria yang segar sekaligus manis, dengan kesan energik dan percaya diri. Cocok untuk acara malam maupun pemakaian santai.',
            ],
            [
                'name'         => 'YSL Y EDP',
                'category'     => 'Pria',
                'price'        => 2400000,
                'image'        => 'images/parfum/ysl-y-edp.png',
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

        // Parfum lain: nama, kategori (perkiraan, silakan koreksi), dan nama file gambar.
        $basic = [
            ['MFK 540 Baccarat Rouge',          'Unisex', 'mfk-540-baccarat-rouge'],
            ['PDM Althair',                      'Pria',   'pdm-althair'],
            ['PDM Delina',                       'Wanita', 'pdm-delina'],
            ['JPG Le Male Elixir',               'Pria',   'jpg-le-male-elixir'],
            ['JPG Le Male Le Parfum',            'Pria',   'jpg-le-male-le-parfum'],
            ['LV Symphony',                      'Unisex', 'lv-symphony'],
            ['LV Imagination',                   'Pria',   'lv-imagination'],
            ['Bleu De Chanel',                   'Pria',   'bleu-de-chanel'],
            ['YSL Black Opium',                  'Wanita', 'ysl-black-opium'],
            ['Chanel No.5',                      'Wanita', 'chanel-no-5'],
            ['Jo Malone English Pear & Freesia', 'Unisex', 'jo-malone-english-pear-freesia'],
            ['Dior Sauvage',                     'Pria',   'dior-sauvage'],
        ];

        foreach ($basic as [$name, $category, $slug]) {
            Perfume::updateOrCreate(
                ['name' => $name],
                ['category' => $category, 'image' => "images/parfum/{$slug}.png"]
            );
        }
    }
}