<?php

namespace App\Models;

use Framework\Core\Model;

class PrihlaskaKurz extends Model
{
    protected ?int $id_prihlaska = null;
    protected ?int $id_osoba = null;
    protected ?int $id_kurz = null;

    protected ?string $stav = null;
    protected ?string $created_at = null;

    protected ?string $zastupca_meno = null;
    protected ?string $zastupca_priezvisko = null;
    protected ?string $zastupca_email = null;
    protected ?string $zastupca_telefon = null;

    protected static function getPkColumnName(): string
    {
        return 'id_prihlaska';
    }

    public static function getTableName(): string
    {
        return 'prihlaska_kurz';
    }

    public function getId(): ?int
    {
        return $this->id_prihlaska;
    }

    public function getIdOsoba(): ?int
    {
        return $this->id_osoba;
    }

    public function setIdOsoba(int $id_osoba): void
    {
        $this->id_osoba = $id_osoba;
    }

    public function getIdKurz(): ?int
    {
        return $this->id_kurz;
    }

    public function setIdKurz(int $id_kurz): void
    {
        $this->id_kurz = $id_kurz;
    }

    public function getStav(): ?string
    {
        return $this->stav;
    }

    public function setStav(string $stav): void
    {
        $this->stav = $stav;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function getZastupcaMeno(): ?string
    {
        return $this->zastupca_meno;
    }

    public function setZastupcaMeno(?string $value): void
    {
        $this->zastupca_meno = $value;
    }

    public function getZastupcaPriezvisko(): ?string
    {
        return $this->zastupca_priezvisko;
    }

    public function setZastupcaPriezvisko(?string $value): void
    {
        $this->zastupca_priezvisko = $value;
    }

    public function getZastupcaEmail(): ?string
    {
        return $this->zastupca_email;
    }

    public function setZastupcaEmail(?string $value): void
    {
        $this->zastupca_email = $value;
    }

    public function getZastupcaTelefon(): ?string
    {
        return $this->zastupca_telefon;
    }

    public function setZastupcaTelefon(?string $value): void
    {
        $this->zastupca_telefon = $value;
    }
}
