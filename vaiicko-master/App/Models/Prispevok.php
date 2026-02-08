<?php

namespace App\Models;

use Framework\Core\Model;
use Framework\DB\Connection;

class Prispevok extends Model
{
    protected ?int $id_prispevok = null;
    protected ?string $nazov = null;
    protected ?string $obsah = null;
    protected ?string $created_at = null;

    // enum('verejny','obdobie','skupina','udalost')
    protected ?string $viditelnost = 'verejny';

    protected ?int $id_obdobie = null;
    protected ?int $id_skupina = null;
    protected ?int $id_udalost = null;

    public static function getPkColumnName(): string
    {
        return 'id_prispevok';
    }

    public static function getTableName(): string
    {
        return 'prispevok';
    }

    public function getId(): ?int { return $this->id_prispevok; }

    public function getNazov(): ?string { return $this->nazov; }
    public function setNazov(?string $nazov): void { $this->nazov = $nazov; }

    public function getObsah(): ?string { return $this->obsah; }
    public function setObsah(?string $obsah): void { $this->obsah = $obsah; }

    public function getCreatedAt(): ?string { return $this->created_at; }

    public function getViditelnost(): ?string { return $this->viditelnost; }
    public function setViditelnost(?string $viditelnost): void { $this->viditelnost = $viditelnost; }

    public function getIdObdobie(): ?int { return $this->id_obdobie; }
    public function setIdObdobie(?int $id): void { $this->id_obdobie = $id; }

    public function getIdSkupina(): ?int { return $this->id_skupina; }
    public function setIdSkupina(?int $id): void { $this->id_skupina = $id; }

    public function getIdUdalost(): ?int { return $this->id_udalost; }
    public function setIdUdalost(?int $id): void { $this->id_udalost = $id; }


    public static function getAllPublic(): array
    {
        return self::getAll(
            'viditelnost = :v',
            ['v' => 'verejny'],
            'created_at DESC'
        );
    }

    public static function getAllForOsoba(int $idOsoba, int $idObdobie): array
    {
        $where = "
            (viditelnost = 'verejny')
            OR (viditelnost = 'obdobie' AND id_obdobie = :o)
            OR (
                viditelnost = 'skupina'
                AND id_skupina IN (
                    SELECT s.id_skupina
                    FROM osoba_skupina os
                    JOIN skupina s ON s.id_skupina = os.id_skupina
                    WHERE os.id_osoba = :os
                      AND s.id_obdobie = :o
                )
            )
            OR (
                viditelnost = 'udalost'
                AND id_udalost IN (
                    SELECT us.id_udalost
                    FROM udalost_skupina us
                    JOIN osoba_skupina os ON os.id_skupina = us.id_skupina
                    JOIN udalost u ON u.id_udalost = us.id_udalost
                    WHERE os.id_osoba = :os
                      AND u.id_obdobie = :o
                )
            )
        ";

        return self::getAll($where, ['os' => $idOsoba, 'o' => $idObdobie], 'created_at DESC');

    }

    /** @return self[] */
    public static function getAllForOsobaNonPublic(int $idOsoba, int $idObdobie): array
    {
        $where = "
        (viditelnost = 'obdobie' AND id_obdobie = :o)
        OR (
            viditelnost = 'skupina'
            AND id_skupina IN (
                SELECT s.id_skupina
                FROM osoba_skupina os
                JOIN skupina s ON s.id_skupina = os.id_skupina
                WHERE os.id_osoba = :os
                  AND s.id_obdobie = :o
            )
        )
        OR (
            viditelnost = 'udalost'
            AND id_udalost IN (
                SELECT us.id_udalost
                FROM udalost_skupina us
                JOIN osoba_skupina os ON os.id_skupina = us.id_skupina
                JOIN udalost u ON u.id_udalost = us.id_udalost
                WHERE os.id_osoba = :os
                  AND u.id_obdobie = :o
            )
        )
    ";

        return self::getAll($where, ['os' => $idOsoba, 'o' => $idObdobie], 'created_at DESC');
    }



    public static function getOnePublic(int $idPrispevok): ?self
    {
        $rows = self::getAll(
            'id_prispevok = :id AND viditelnost = :v',
            ['id' => $idPrispevok, 'v' => 'verejny'],
            'id_prispevok ASC'
        );

        return $rows[0] ?? null;
    }



    public static function getOneForOsoba(int $idPrispevok, int $idOsoba, int $idObdobie): ?self
    {
        $where = "
        id_prispevok = :id AND (
                (viditelnost = 'verejny')
                OR (viditelnost = 'obdobie' AND id_obdobie = :o)
            OR (
                viditelnost = 'skupina'
                AND id_skupina IN (
                SELECT s.id_skupina
                    FROM osoba_skupina os
                    JOIN skupina s ON s.id_skupina = os.id_skupina
                    WHERE os.id_osoba = :os
            AND s.id_obdobie = :o
                )
            )
            OR (
                viditelnost = 'udalost'
                AND id_udalost IN (
                SELECT us.id_udalost
                    FROM udalost_skupina us
                    JOIN osoba_skupina os ON os.id_skupina = us.id_skupina
                    JOIN udalost u ON u.id_udalost = us.id_udalost
                    WHERE os.id_osoba = :os
            AND u.id_obdobie = :o
                )
            )
            )
        ";


        $rows = self::getAll(
            $where,
            ['id' => $idPrispevok, 'os' => $idOsoba, 'o' => $idObdobie],
            'id_prispevok ASC'
        );

        return $rows[0] ?? null;
    }


}
