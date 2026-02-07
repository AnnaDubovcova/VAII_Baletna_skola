<?php

namespace App\Models;

use Framework\Core\Model;

class UdalostUcast extends Model
{
    protected ?int $id_udalost = null;
    protected ?int $id_osoba = null;
    protected ?string $stav = null; // 'ucast' | 'neucast'
    protected ?string $updated_at = null;

    protected static function getTableName(): string
    {
        return 'udalost_ucast';
    }

    // Kompozitný PK – nepoužívame getOne/save klasicky.

    public static function getStav(int $idUdalost, int $idOsoba): ?string
    {
        $rows = self::getAll(
            'id_udalost = :u AND id_osoba = :o',
            ['u' => $idUdalost, 'o' => $idOsoba],
            'updated_at DESC',
            1
        );
        if (empty($rows)) {
            return null;
        }
        /** @var self $r */
        $r = $rows[0];
        return $r->stav;
    }

    public static function setStav(int $idUdalost, int $idOsoba, string $stav): void
    {
        if (!in_array($stav, ['ucast', 'neucast'], true)) {
            throw new \Exception('Neplatný stav účasti.');
        }

        // MariaDB/MySQL: INSERT ... ON DUPLICATE KEY UPDATE
        self::executeRawSQL(
            "INSERT INTO udalost_ucast (id_udalost, id_osoba, stav)
             VALUES (:u, :o, :s)
             ON DUPLICATE KEY UPDATE stav = VALUES(stav)",
            ['u' => $idUdalost, 'o' => $idOsoba, 's' => $stav]
        );
    }

    public static function deleteFor(int $idUdalost, int $idOsoba): void
    {
        self::executeRawSQL(
            "DELETE FROM udalost_ucast WHERE id_udalost = :u AND id_osoba = :o",
            ['u' => $idUdalost, 'o' => $idOsoba]
        );
    }
}
