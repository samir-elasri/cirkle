<?php
// Génère le favicon Cirkle (le « globe » du logo) — remplace l'ancien favicon mbiance.
// GD only (pas d'ImageMagick sur ce poste). Écrit favicon.ico (PNG embarqué) + favicon-cirkle.png.

$logo = __DIR__ . '/../public_html/medias/setting/main_logo_image/fr_Cirkle-services-logo-2023-1080p_petit.png';
$src = imagecreatefrompng($logo);
$W = imagesx($src); $H = imagesy($src);

// Le globe occupe le carré gauche du logo (hauteur = côté). Petite marge incluse.
$cropSize = $H;                    // 132
$crop = imagecreatetruecolor($cropSize, $cropSize);
imagealphablending($crop, false);
imagesavealpha($crop, true);
imagefill($crop, 0, 0, imagecolorallocatealpha($crop, 0, 0, 0, 127));
imagecopy($crop, $src, 0, 0, 0, 0, min($cropSize, $W), $cropSize);

function sizePng($crop, $cropSize, $size): string {
    $img = imagecreatetruecolor($size, $size);
    imagealphablending($img, false);
    imagesavealpha($img, true);
    imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
    imagecopyresampled($img, $crop, 0, 0, 0, 0, $size, $size, $cropSize, $cropSize);
    ob_start(); imagepng($img); $bytes = ob_get_clean();
    imagedestroy($img);
    return $bytes;
}

$png64 = sizePng($crop, $cropSize, 64);
$png32 = sizePng($crop, $cropSize, 32);
$png16 = sizePng($crop, $cropSize, 16);

// favicon-cirkle.png (64x64) pour le <link rel=icon type=png>
file_put_contents(__DIR__ . '/../public_html/favicon-cirkle.png', $png64);

// favicon.ico : conteneur ICO avec PNG embarqués (supporté navigateurs modernes + Windows)
$entries = [[64, $png64], [32, $png32], [16, $png16]];
$count = count($entries);
$ico = pack('v3', 0, 1, $count);          // ICONDIR : reserved=0, type=1(icon), count
$offset = 6 + 16 * $count;
$blob = '';
foreach ($entries as [$sz, $png]) {
    $dim = $sz >= 256 ? 0 : $sz;
    $ico .= pack('C4', $dim, $dim, 0, 0) . pack('v2', 1, 32) . pack('V2', strlen($png), $offset);
    $offset += strlen($png);
    $blob .= $png;
}
$ico .= $blob;
file_put_contents(__DIR__ . '/../public_html/favicon.ico', $ico);

echo "OK: favicon.ico (" . strlen($ico) . " bytes) + favicon-cirkle.png (" . strlen($png64) . " bytes)\n";
