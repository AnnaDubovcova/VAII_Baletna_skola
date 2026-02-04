<?php

namespace App\Models;

use Framework\Core\Model;
use Framework\DB\Connection;

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

}
