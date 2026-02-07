<?php

namespace App\Models;

use Framework\Core\Model;

class Prispevok extends Model
{
    protected ?int $id_prispevok = null;
    protected ?string $nazov = null;
    protected ?string $obsah = null;
    protected ?string $created_at = null;

    // enum('verejny','obdobie','skupina','udalost')
    protected ?string $viditelnost = 'verejny';

    protected ?int $id_obdobie = null;
    protected ?int $id_skupina = null;
    protected ?int $id_udalost = null;

    public static function getPkColumnName(): string
    {
        return 'id_prispevok';
    }

    public static function getTableName(): string
    {
        return 'prispevok';
    }

    public function getId(): ?int { return $this->id_prispevok; }

    public function getNazov(): ?string { return $this->nazov; }
    public function setNazov(?string $nazov): void { $this->nazov = $nazov; }

    public function getObsah(): ?string { return $this->obsah; }
    public function setObsah(?string $obsah): void { $this->obsah = $obsah; }

    public function getCreatedAt(): ?string { return $this->created_at; }

    public function getViditelnost(): ?string { return $this->viditelnost; }
    public function setViditelnost(?string $viditelnost): void { $this->viditelnost = $viditelnost; }

    public function getIdObdobie(): ?int { return $this->id_obdobie; }
    public function setIdObdobie(?int $id): void { $this->id_obdobie = $id; }

    public function getIdSkupina(): ?int { return $this->id_skupina; }
    public function setIdSkupina(?int $id): void { $this->id_skupina = $id; }

    public function getIdUdalost(): ?int { return $this->id_udalost; }
    public function setIdUdalost(?int $id): void { $this->id_udalost = $id; }

    /**
     * Verejné príspevky (bez loginu).
     */
    public static function getPublic(): array
    {
        return self::getAll(
            'viditelnost = :v',
            ['v' => 'verejny'],
            'created_at DESC'
        );
    }
}
