<?php

namespace App\Models;

use Framework\Core\Model;
use Framework\DB\Connection;

class Skupina extends Model
{
    // Názvy premenných musia sedieť so stĺpcami v DB
    protected ?int $id_skupina = null;
    protected ?string $nazov = null;
    protected ?int $id_obdobie = null;
    protected ?string $popis = null;

    protected static function getPkColumnName(): string
    {
        return 'id_skupina';
    }

    public static function getTableName(): string
    {
        return 'skupina';
    }

    public function getId(): ?int
    {
        return $this->id_skupina;
    }

    public function getNazov(): ?string
    {
        return $this->nazov;
    }

    public function setNazov(string $nazov): void
    {
        $this->nazov = $nazov;
    }

    public function getIdObdobie(): ?int
    {
        return $this->id_obdobie;
    }

    public function setIdObdobie(int $idObdobie): void
    {
        $this->id_obdobie = $idObdobie;
    }

    public function getPopis(): ?string
    {
        return $this->popis;
    }

    public function setPopis(?string $popis): void
    {
        $this->popis = $popis;
    }

    /**
     * Zistí, či daná osoba patrí do tejto skupiny.
     */
    public function hasMember(int $idOsoba): bool
    {
        $con = Connection::getInstance();

        $stmt = $con->prepare(
            'SELECT 1
             FROM osoba_skupina
             WHERE id_skupina = :s AND id_osoba = :o
             LIMIT 1'
        );

        $stmt->execute([
            's' => $this->getId(),
            'o' => $idOsoba,
        ]);

        return (bool)$stmt->fetchColumn();
    }


    /*
     * Vráti osoby patriace do tejto skupiny.
     *
     * @return Osoba[]
     */
    public function getMembers(): array
    {
        $con = Connection::getInstance();
        $stmt = $con->prepare(
            'SELECT o.*
             FROM osoba o
             JOIN osoba_skupina os ON os.id_osoba = o.id_osoba
             WHERE os.id_skupina = :s
             ORDER BY o.priezvisko, o.meno'
        );
        $stmt->execute(['s' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, Osoba::class);
    }

    /**
     * Kandidáti na pridanie do skupiny:
     * - majú schválenú prihlášku
     * - kurz patrí do aktívneho obdobia
     * - ešte nie sú v skupine
     * - voliteľné vyhľadávanie
     */
    public function getCandidateOsoby(int $idObdobie, ?string $q = null): array
    {
        $sql = '
            SELECT DISTINCT o.*
            FROM osoba o
            JOIN prihlaska_kurz pk ON pk.id_osoba = o.id_osoba
            JOIN kurz k ON k.id_kurz = pk.id_kurz
            WHERE
                k.id_obdobie = :o
                AND pk.stav = \'schvalena\'
                AND o.id_osoba NOT IN (
                    SELECT id_osoba
                    FROM osoba_skupina
                    WHERE id_skupina = :s
                )
        ';

        $params = [
            'o' => $idObdobie,
            's' => $this->getId(),
        ];

        if ($q !== null && $q !== '') {
            $sql .= ' AND (o.meno LIKE :q OR o.priezvisko LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }

        $sql .= ' ORDER BY o.priezvisko, o.meno';

        $con = Connection::getInstance();
        $stmt = $con->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, Osoba::class);
    }

    /**
     * Pridanie osoby do skupiny.
     */
    public function addOsoba(int $idOsoba): void
    {
        $con = Connection::getInstance();
        $con->prepare(
            'INSERT IGNORE INTO osoba_skupina (id_osoba, id_skupina)
             VALUES (:o, :s)'
        )->execute([
            'o' => $idOsoba,
            's' => $this->getId(),
        ]);
    }

    /**
     * Odobratie osoby zo skupiny.
     */
    public function removeOsoba(int $idOsoba): void
    {
        $con = Connection::getInstance();
        $con->prepare(
            'DELETE FROM osoba_skupina
             WHERE id_osoba = :o AND id_skupina = :s'
        )->execute([
            'o' => $idOsoba,
            's' => $this->getId(),
        ]);
    }
}
