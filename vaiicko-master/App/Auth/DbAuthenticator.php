<?php

namespace App\Auth;

use Framework\Auth\SessionAuthenticator;
use Framework\Core\IIdentity;
use Framework\DB\Connection;
use PDO;

class DbAuthenticator extends SessionAuthenticator
{
    protected function authenticate(string $username, string $password): ?IIdentity
    {
        // Username je u nas email.
        $email = trim($username);

        if ($email === '' || $password === '') {
            return null;
        }

        $sql = $sql = "SELECT `id_pouzivatel`, `email`, `password_hash`, `rola`
        FROM `pouzivatel`
        WHERE `email` = :email
        LIMIT 1";


        $stmt = Connection::getInstance()->prepare($sql);
        $stmt->execute(['email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            // Pouzivatel s danym emailom neexistuje.
            return null;
        }

        // Overime heslo cez password_verify proti hash-u v DB.
        if (!password_verify($password, (string)$row['password_hash'])) {
            return null;
        }

        // Pri uspechu vratime identity objekt (ulozi sa do session).
        return new PouzivatelIdentity(
            (int)$row['id_pouzivatel'],
            (string)$row['email'],
            (string)$row['rola']
        );
    }
}
