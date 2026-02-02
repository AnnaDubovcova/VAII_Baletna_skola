<?php

namespace App\Models;

use Framework\Core\Model;

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

    public function getCreatedAt(): ?string { return $this->created_at; }
}
