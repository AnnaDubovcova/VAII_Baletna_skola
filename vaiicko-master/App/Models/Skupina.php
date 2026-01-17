<?php

namespace App\Models;

use Framework\Core\Model;

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
}
