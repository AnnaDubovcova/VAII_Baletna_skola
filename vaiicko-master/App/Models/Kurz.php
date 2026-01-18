<?php

namespace App\Models;
use Framework\Core\Model;

class Kurz extends Model
{
    protected ?int $id_kurz = null;
    protected ?string $nazov = null;
    protected ?int $id_typ_kurzu = null;
    protected ?int $id_obdobie = null;
    protected ?string $popis = null;
    protected ?float $cena = null;
    protected ?int $prihlasovanie_otvorene = 0;


    protected static function getPkColumnName(): string
    {
        return 'id_kurz';
    }

    public static function getTableName(): string
    {
        return 'kurz';
    }

    public function getId(): ?int
    {
        return $this->id_kurz;
    }

    public function getNazov(): ?string
    {
        return $this->nazov;
    }

    public function setNazov(string $nazov): void
    {
        $this->nazov = $nazov;
    }

    public function getIdTypKurzu(): ?int
    {
        return $this->id_typ_kurzu;
    }

    public function setIdTypKurzu(int $idTypKurzu): void
    {
        $this->id_typ_kurzu = $idTypKurzu;
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

    public function getCena(): ?float
    {
        return $this->cena;
    }

    public function setCena(?float $cena): void
    {
        $this->cena = $cena;
    }

    public function isPrihlasovanieOtvorene(): ?int
    {
        return $this->prihlasovanie_otvorene;
    }

    public function setPrihlasovanieOtvorene(bool $prihlasovanieOtvorene): void
    {
        $this->prihlasovanie_otvorene = $prihlasovanieOtvorene ? 1 : 0;
    }
}