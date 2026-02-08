<?php

namespace App\Models;

use Framework\Core\Model;

class PrispevokSubor extends Model
{
    protected ?int $id_prispevok_subor = null;
    protected ?int $id_prispevok = null;
    protected ?string $original_name = null;
    protected ?string $stored_name = null;
    protected ?string $mime_type = null;
    protected ?int $size = null;
    protected ?string $created_at = null;

    protected static function getPkColumnName(): string
    {
        return 'id_prispevok_subor';
    }

    public static function getTableName(): string
    {
        return 'prispevok_subor';
    }

    public function getId(): ?int { return $this->id_prispevok_subor; }

    public function getIdPrispevok(): ?int { return $this->id_prispevok; }
    public function setIdPrispevok(int $id): void { $this->id_prispevok = $id; }

    public function getOriginalName(): ?string { return $this->original_name; }
    public function setOriginalName(string $s): void { $this->original_name = $s; }

    public function getStoredName(): ?string { return $this->stored_name; }
    public function setStoredName(string $s): void { $this->stored_name = $s; }

    public function getMimeType(): ?string { return $this->mime_type; }
    public function setMimeType(string $s): void { $this->mime_type = $s; }

    public function getSize(): ?int { return $this->size; }
    public function setSize(int $n): void { $this->size = $n; }

    public function getCreatedAt(): ?string { return $this->created_at; }

    /** @return self[] */
    public static function getAllForPrispevok(int $idPrispevok): array
    {
        return self::getAll(
            'id_prispevok = :p',
            ['p' => $idPrispevok],
            'created_at DESC'
        );
    }
}
