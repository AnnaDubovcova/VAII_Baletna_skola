<?php

namespace App\Auth;

use Framework\Core\IIdentity;

class PouzivatelIdentity implements IIdentity
{
    private int $id_pouzivatel;
    private string $email;
    private string $rola;

    public function __construct(int $id_pouzivatel, string $email, string $rola)
    {
        $this->id_pouzivatel = $id_pouzivatel;
        $this->email = $email;
        $this->rola = $rola;
    }

    // Povinna metoda z IIdentity. Ako meno identity pouzijeme email.
    public function getName(): string
    {
        return $this->email;
    }

    // Pomocne metody pre autorizaciu a pracu s uctom.
    public function getIdPouzivatel(): int
    {
        return $this->id_pouzivatel;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRola(): string
    {
        return $this->rola;
    }

    public function isAdmin(): bool
    {
        return $this->rola === 'admin';
    }
}
