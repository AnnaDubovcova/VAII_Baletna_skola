<?php

namespace App\Models;

use Framework\Core\Model;

class Obdobie extends Model
{
    // Musia sa volať rovnako ako stĺpce v DB
    protected ?int $id_obdobie = null;
    protected ?string $nazov = null;
    protected ?string $datum_od = null;   // DATE v DB, string v PHP
    protected ?string $datum_do = null;
    protected ?string $popis = null;

    // Ak je tabuľka pomenovaná inak, než by framework čakal (napr. nie "obdobies"),
    // takto mu povieme správny názov:

    protected static function getPkColumnName(): string
    {
        return 'id_obdobie';
    }
    public static function getTableName(): string
    {
        return 'obdobie';
    }

    public function getId(): ?int
    {
        return $this->id_obdobie;
    }

    public function getNazov(): ?string
    {
        return $this->nazov;
    }

    public function setNazov(string $nazov): void
    {
        $this->nazov = $nazov;
    }

    public function getDatumOd(): ?string
    {
        return $this->datum_od;
    }

    public function setDatumOd(string $datumOd): void
    {
        $this->datum_od = $datumOd;
    }

    public function getDatumDo(): ?string
    {
        return $this->datum_do;
    }

    public function setDatumDo(string $datumDo): void
    {
        $this->datum_do = $datumDo;
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
