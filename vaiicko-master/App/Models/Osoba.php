<?php

namespace App\Models;

use Framework\Core\Model;

class Osoba extends Model
{
    protected ?int $id_osoba = null;
    protected ?int $id_pouzivatel = null;

    protected ?string $meno = null;
    protected ?string $priezvisko = null;
    protected ?string $datum_narodenia = null;

    protected ?string $email = null;
    protected ?string $telefon = null;

    protected ?string $zastupca_meno = null;
    protected ?string $zastupca_priezvisko = null;
    protected ?string $zastupca_email = null;
    protected ?string $zastupca_telefon = null;

    protected ?string $created_at = null;

    protected static function getPkColumnName(): string
    {
        return 'id_osoba';
    }

    public static function getTableName(): string
    {
        return 'osoba';
    }

    public function getId(): ?int
    {
        return $this->id_osoba;
    }

    public function getIdPouzivatel(): ?int
    {
        return $this->id_pouzivatel;
    }

    public function setIdPouzivatel(int $id_pouzivatel): void
    {
        $this->id_pouzivatel = $id_pouzivatel;
    }

    public function getMeno(): ?string
    {
        return $this->meno;
    }

    public function setMeno(string $meno): void
    {
        $this->meno = $meno;
    }

    public function getPriezvisko(): ?string
    {
        return $this->priezvisko;
    }

    public function setPriezvisko(string $priezvisko): void
    {
        $this->priezvisko = $priezvisko;
    }

    public function getDatumNarodenia(): ?string
    {
        return $this->datum_narodenia;
    }

    public function setDatumNarodenia(string $datum_narodenia): void
    {
        $this->datum_narodenia = $datum_narodenia;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getTelefon(): ?string
    {
        return $this->telefon;
    }

    public function setTelefon(?string $telefon): void
    {
        $this->telefon = $telefon;
    }

    public function getZastupcaMeno(): ?string
    {
        return $this->zastupca_meno;
    }

    public function setZastupcaMeno(?string $zastupca_meno): void
    {
        $this->zastupca_meno = $zastupca_meno;
    }

    public function getZastupcaPriezvisko(): ?string
    {
        return $this->zastupca_priezvisko;
    }

    public function setZastupcaPriezvisko(?string $zastupca_priezvisko): void
    {
        $this->zastupca_priezvisko = $zastupca_priezvisko;
    }

    public function getZastupcaEmail(): ?string
    {
        return $this->zastupca_email;
    }

    public function setZastupcaEmail(?string $zastupca_email): void
    {
        $this->zastupca_email = $zastupca_email;
    }

    public function getZastupcaTelefon(): ?string
    {
        return $this->zastupca_telefon;
    }

    public function setZastupcaTelefon(?string $zastupca_telefon): void
    {
        $this->zastupca_telefon = $zastupca_telefon;
    }
}
