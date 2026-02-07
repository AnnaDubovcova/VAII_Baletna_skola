<?php

namespace App\Models;

use Framework\Core\Model;
use Framework\DB\Connection;
use PDO;

class Udalost extends Model
{
    protected ?int $id_udalost = null;
    protected ?string $nazov = null;
    protected ?string $typ = 'trening';     // enum('trening','nacvik','vystupenie','ine')
    protected ?string $zaciatok = null;     // datetime
    protected ?string $koniec = null;       // datetime|null
    protected ?string $miesto = null;
    protected ?string $popis = null;
    protected ?string $created_at = null;

    protected ?int $id_obdobie = null; // FK na obdobie, v ktorom sa udalost koná

    protected static function getPkColumnName(): string
    {
        return 'id_udalost';
    }

    public static function getTableName(): string
    {
        return 'udalost';
    }

    public function getId(): ?int { return $this->id_udalost; }

    public function getNazov(): ?string { return $this->nazov; }
    public function setNazov(string $nazov): void { $this->nazov = $nazov; }

    public function getTyp(): ?string { return $this->typ; }
    public function setTyp(string $typ): void { $this->typ = $typ; }

    public function getZaciatok(): ?string { return $this->zaciatok; }
    public function setZaciatok(string $zaciatok): void { $this->zaciatok = $zaciatok; }

    public function getKoniec(): ?string { return $this->koniec; }
    public function setKoniec(?string $koniec): void { $this->koniec = $koniec; }

    public function getMiesto(): ?string { return $this->miesto; }
    public function setMiesto(?string $miesto): void { $this->miesto = $miesto; }

    public function getPopis(): ?string { return $this->popis; }
    public function setPopis(?string $popis): void { $this->popis = $popis; }

    public function getIdObdobie(): int
    {
        return (int)$this->id_obdobie;
    }

    public function setIdObdobie(int $idObdobie): void
    {
        $this->id_obdobie = $idObdobie;
    }



    public function getCreatedAt(): ?string { return $this->created_at; }


    /*
     * Vráti ID skupín priradených k udalosti.
     */
    public function getSkupinaIds(): array
    {
        $con = Connection::getInstance();
        $stmt = $con->prepare(
            'SELECT id_skupina FROM udalost_skupina WHERE id_udalost = :u'
        );
        $stmt->execute(['u' => $this->getId()]);

        return array_map(
            fn($r) => (int)$r['id_skupina'],
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    /**
     * Synchronizuje skupiny udalosti (DELETE + INSERT).
     */
    public function syncSkupiny(array $skupinaIds): void
    {
        $con = Connection::getInstance();
        $con->beginTransaction();

        try {
            $con->prepare(
                'DELETE FROM udalost_skupina WHERE id_udalost = :u'
            )->execute(['u' => $this->getId()]);

            if (!empty($skupinaIds)) {
                $stmt = $con->prepare(
                    'INSERT INTO udalost_skupina (id_udalost, id_skupina)
                     VALUES (:u, :s)'
                );

                foreach ($skupinaIds as $sid) {
                    $stmt->execute([
                        'u' => $this->getId(),
                        's' => (int)$sid,
                    ]);
                }
            }

            $con->commit();
        } catch (\Throwable $e) {
            $con->rollBack();
            throw $e;
        }
    }


    /**
     * User: udalosti v týždni pre konkrétnu osobu (cez členstvo v skupinách).
     */
    public static function getWeekForOsoba(int $idOsoba, int $idObdobie, string $from, string $to): array
    {
        $con = Connection::getInstance();

        $sql = "
            SELECT
                u.id_udalost,
                u.nazov,
                u.typ,
                u.zaciatok,
                u.koniec,
                u.miesto,
                u.popis,
                s.id_skupina,
                s.nazov AS skupina_nazov
            FROM udalost u
            JOIN udalost_skupina us ON us.id_udalost = u.id_udalost
            JOIN skupina s ON s.id_skupina = us.id_skupina
            JOIN osoba_skupina os ON os.id_skupina = s.id_skupina
            WHERE
                os.id_osoba = :id_osoba
                AND u.id_obdobie = :id_obdobie
                AND u.zaciatok >= :from_dt
                AND u.zaciatok <  :to_dt
            ORDER BY u.zaciatok ASC, s.nazov ASC
        ";

        $stmt = $con->prepare($sql);
        $stmt->execute([
            'id_osoba' => $idOsoba,
            'id_obdobie' => $idObdobie,
            'from_dt' => $from,
            'to_dt' => $to,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Admin: všetky udalosti v týždni v aktívnom období.
     * (Skupiny pospájané do jedného textu pre pekný rozvrh.)
     */
    public static function getWeekForAdmin(int $idObdobie, string $from, string $to): array
    {
        $con = Connection::getInstance();

        $sql = "
            SELECT
                u.id_udalost,
                u.nazov,
                u.typ,
                u.zaciatok,
                u.koniec,
                u.miesto,
                u.popis,
                GROUP_CONCAT(s.nazov ORDER BY s.nazov SEPARATOR ', ') AS skupiny
            FROM udalost u
            LEFT JOIN udalost_skupina us ON us.id_udalost = u.id_udalost
            LEFT JOIN skupina s ON s.id_skupina = us.id_skupina
            WHERE
                u.id_obdobie = :id_obdobie
                AND u.zaciatok >= :from_dt
                AND u.zaciatok <  :to_dt
            GROUP BY
                u.id_udalost,
                u.nazov,
                u.typ,
                u.zaciatok,
                u.koniec,
                u.miesto,
                u.popis
            ORDER BY u.zaciatok ASC

        ";

        $stmt = $con->prepare($sql);
        $stmt->execute([
            'id_obdobie' => $idObdobie,
            'from_dt' => $from,
            'to_dt' => $to,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOneForOsoba(int $idUdalost, int $idOsoba, int $idObdobie): ?array
    {
        $con = Connection::getInstance();

        $sql = "
        SELECT
            u.id_udalost,
            u.nazov,
            u.typ,
            u.zaciatok,
            u.koniec,
            u.miesto,
            u.popis,
            GROUP_CONCAT(s.nazov ORDER BY s.nazov SEPARATOR ', ') AS skupiny
        FROM udalost u
        JOIN udalost_skupina us ON us.id_udalost = u.id_udalost
        JOIN skupina s ON s.id_skupina = us.id_skupina
        JOIN osoba_skupina os ON os.id_skupina = s.id_skupina
        WHERE
            u.id_udalost = :id_udalost
            AND os.id_osoba = :id_osoba
            AND u.id_obdobie = :id_obdobie
        GROUP BY
            u.id_udalost,
            u.nazov,
            u.typ,
            u.zaciatok,
            u.koniec,
            u.miesto,
            u.popis
        LIMIT 1
    ";

        $stmt = $con->prepare($sql);
        $stmt->execute([
            'id_udalost' => $idUdalost,
            'id_osoba' => $idOsoba,
            'id_obdobie' => $idObdobie,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }



}
