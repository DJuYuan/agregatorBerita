<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

$catWisata = Category::where('slug', 'wisata-jogja')->first();
if ($catWisata) {
    $catWisata->update(['keywords' => 'wisata, destinasi, pantai, candi, liburan, hotel, penginapan']);
    echo "Wisata updated.\n";
}

$catKuliner = Category::where('slug', 'kuliner-jogja')->first();
if ($catKuliner) {
    $catKuliner->update(['keywords' => 'kuliner, makanan, soto, gudeg, warung, kopi, masakan, bakso']);
    echo "Kuliner updated.\n";
}

$catHiburan = Category::where('slug', 'hiburan-film')->first();
if ($catHiburan) {
    $catHiburan->update(['keywords' => 'hiburan, film, bioskop, konser, musik']);
    echo "Hiburan updated.\n";
}
