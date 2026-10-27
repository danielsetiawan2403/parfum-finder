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
        // Parfum dengan data lengkap (data contoh, silakan sesuaikan).
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

        // Parfum lain: semua data sudah lengkap (harga = perkiraan harga pasar, silakan sesuaikan).
        $others = [
            [
                'name'         => 'MFK 540 Baccarat Rouge',
                'category'     => 'Unisex',
                'price'        => 5200000,
                'image'        => 'images/parfum/mkf-540.png',
                'top_notes'    => 'Saffron, Jasmine.',
                'middle_notes' => 'Amberwood, Ambergris.',
                'base_notes'   => 'Fir Resin, Cedar.',
                'description'  => 'Baccarat Rouge 540 adalah wewangian amber floral yang mewah, manis, dan sedikit mineral. Sillage-nya kuat dan mudah dikenali, cocok untuk acara spesial maupun malam hari.',
            ],
            [
                'name'         => 'PDM Althair',
                'category'     => 'Pria',
                'price'        => 4300000,
                'image'        => 'images/parfum/pdm.png',
                'top_notes'    => 'Cinnamon, Cardamom, Bergamot.',
                'middle_notes' => 'Bourbon Vanilla, Praline.',
                'base_notes'   => 'Musk, Sandalwood, Ambroxan.',
                'description'  => 'Parfums de Marly Althair adalah wewangian vanila hangat dengan sentuhan rempah dan praline. Terkesan manis, mewah, dan nyaman, cocok untuk malam hari dan cuaca sejuk.',
            ],
            [
                'name'         => 'PDM Delina',
                'category'     => 'Wanita',
                'price'        => 4200000,
                'image'        => 'images/parfum/pdm-delina.png',
                'top_notes'    => 'Lychee, Rhubarb, Bergamot.',
                'middle_notes' => 'Turkish Rose, Peony, Musk.',
                'base_notes'   => 'Cashmeran, Cedarwood, Incense, Vanilla.',
                'description'  => 'Parfums de Marly Delina adalah wewangian floral feminin yang segar dan manis, didominasi mawar dan leci. Elegan dan romantis, cocok untuk pemakaian siang maupun acara formal.',
            ],
            [
                'name'         => 'JPG Le Male Elixir',
                'category'     => 'Pria',
                'price'        => 2300000,
                'image'        => 'images/parfum/jpg-elix.png',
                'top_notes'    => 'Mint, Lavender, Bergamot.',
                'middle_notes' => 'Cinnamon, Cumin.',
                'base_notes'   => 'Tonka Bean, Vanilla, Honey, Tobacco, Benzoin.',
                'description'  => 'Jean Paul Gaultier Le Male Elixir adalah wewangian oriental manis dengan kesan gelap dan sensual. Perpaduan madu, tembakau, dan vanila membuatnya tahan lama dan cocok untuk malam hari.',
            ],
            [
                'name'         => 'JPG Le Male Le Parfum',
                'category'     => 'Pria',
                'price'        => 2000000,
                'image'        => 'images/parfum/jpg-lf.png',
                'top_notes'    => 'Cardamom, Lavender.',
                'middle_notes' => 'Iris, Orange Blossom.',
                'base_notes'   => 'Vanilla, Amber, Woody Notes.',
                'description'  => 'Jean Paul Gaultier Le Male Le Parfum adalah wewangian aromatik oriental yang hangat dan maskulin. Lavender dan iris berpadu dengan vanila-amber, cocok untuk acara malam dan kencan.',
            ],
            [
                'name'         => 'LV Symphony',
                'category'     => 'Unisex',
                'price'        => 5500000,
                'image'        => 'images/parfum/lvs.png',
                'top_notes'    => 'Bergamot, Mandarin Orange.',
                'middle_notes' => 'Ylang-Ylang, Jasmine.',
                'base_notes'   => 'Cedarwood, Musk, Amber.',
                'description'  => 'Louis Vuitton Symphony adalah wewangian unisex yang cerah, bersih, dan elegan. Perpaduan citrus dan floral yang lembut memberi kesan mewah namun tetap nyaman dipakai sehari-hari.',
            ],
            [
                'name'         => 'LV Imagination',
                'category'     => 'Pria',
                'price'        => 5000000,
                'image'        => 'images/parfum/lv.png',
                'top_notes'    => 'Citron, Calabrian Bergamot, Orange.',
                'middle_notes' => 'Ginger, Cinnamon, Black Tea.',
                'base_notes'   => 'Guaiac Wood, Ambroxan, Incense.',
                'description'  => 'Louis Vuitton Imagination adalah wewangian citrus segar dengan sentuhan teh hitam dan rempah. Ringan, bersih, dan elegan, cocok untuk kerja maupun acara santai.',
            ],
            [
                'name'         => 'Bleu De Chanel',
                'category'     => 'Pria',
                'price'        => 2600000,
                'image'        => 'images/parfum/bdc.jpg',
                'top_notes'    => 'Grapefruit, Lemon, Mint, Pink Pepper.',
                'middle_notes' => 'Ginger, Nutmeg, Jasmine.',
                'base_notes'   => 'Incense, Vetiver, Cedar, Sandalwood, Patchouli, White Musk.',
                'description'  => 'Bleu de Chanel adalah wewangian woody aromatik yang segar, bersih, dan maskulin. Serbaguna untuk kantor, harian, maupun acara formal.',
            ],
            [
                'name'         => 'YSL Black Opium',
                'category'     => 'Wanita',
                'price'        => 2000000,
                'image'        => 'images/parfum/bo.jpg',
                'top_notes'    => 'Pink Pepper, Orange Blossom, Pear.',
                'middle_notes' => 'Coffee, Jasmine, Bitter Almond, Licorice.',
                'base_notes'   => 'Vanilla, Patchouli, Cedar, Cashmere Wood.',
                'description'  => 'YSL Black Opium adalah wewangian wanita yang manis, hangat, dan adiktif dengan kopi serta vanila. Terkesan berani dan seksi, cocok untuk malam hari dan jalan-jalan santai.',
            ],
            [
                'name'         => 'Chanel No.5',
                'category'     => 'Wanita',
                'price'        => 3500000,
                'image'        => 'images/parfum/cn5.jpg',
                'top_notes'    => 'Aldehydes, Ylang-Ylang, Neroli, Bergamot, Lemon.',
                'middle_notes' => 'Jasmine, Rose, Lily of the Valley, Iris.',
                'base_notes'   => 'Vanilla, Sandalwood, Amber, Vetiver, Patchouli.',
                'description'  => 'Chanel No.5 adalah wewangian floral aldehyde klasik yang ikonik. Aromanya pudar, elegan, dan timeless, cocok untuk acara formal dan pemakaian sehari-hari.',
            ],
            [
                'name'         => 'Jo Malone English Pear & Freesia',
                'category'     => 'Unisex',
                'price'        => 2100000,
                'image'        => 'images/parfum/jme.jpg',
                'top_notes'    => 'King William Pear, Melon.',
                'middle_notes' => 'Freesia, Rose.',
                'base_notes'   => 'Patchouli, Amber, Rhubarb.',
                'description'  => 'Jo Malone English Pear & Freesia adalah wewangian fruity floral yang segar, lembut, dan ringan. Pir yang juicy berpadu dengan freesia, cocok untuk siang hari dan suasana santai.',
            ],
            [
                'name'         => 'Dior Sauvage',
                'category'     => 'Pria',
                'price'        => 2300000,
                'image'        => 'images/parfum/dior-sauvage.jpg',
                'top_notes'    => 'Calabrian Bergamot, Pepper.',
                'middle_notes' => 'Sichuan Pepper, Lavender, Pink Pepper, Geranium, Patchouli.',
                'base_notes'   => 'Ambroxan, Cedar, Labdanum.',
                'description'  => 'Dior Sauvage adalah wewangian pria yang segar, pedas, dan maskulin. Bergamot berpadu dengan lada dan ambroxan, mudah disukai dan cocok untuk segala suasana.',
            ],
        ];

        foreach ($others as $perfume) {
            Perfume::updateOrCreate(['name' => $perfume['name']], $perfume);
        }
    }
}