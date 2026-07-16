<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

/**
 * Class ini SENGAJA dibuat kosong/simpel.
 * Fungsinya cuma buat "memberitahu" package Laravel Excel bahwa kita mau
 * baca file sebagai array MENTAH (semua baris apa adanya, TERMASUK baris pertama),
 * bukan otomatis menganggap baris 1 sebagai heading (beda dengan WithHeadingRow).
 *
 * Kenapa kita gak pakai WithHeadingRow bawaan package?
 * Karena WithHeadingRow itu MEMAKSA nama kolom Excel harus persis sama dengan
 * nama field di sistem kita. Padahal requirement kita: admin BEBAS pilih kolom
 * mana ketemu field mana -- jadi kita perlu kontrol manual, bukan auto-matching.
 */
class ExcelRawImport implements ToArray
{
    public function array(array $array): array
    {
        return $array;
    }
}