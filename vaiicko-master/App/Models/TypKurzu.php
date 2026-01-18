<?php

namespace App\Models;

use Framework\Core\Model;

class TypKurzu extends Model
{
    // nazvy premennyc
    protected ?int $id_typ_kurzu = null;
    protected ?string $nazov = null;
    protected ?string $popis = null;

    protected static function getPkColumnName(): string
    {
        return 'id_typ_kurzu';
    }
    public static function getTableName(): string
    {
        return 'typ_kurzu';
    }
    public function getId(): ?int
    {
        return $this->id_typ_kurzu;
    }

    public function getNazov(): ?string
    {
        return $this->nazov;
    }

    public function setNazov(string $nazov): void
    {
        $this->nazov = $nazov;
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