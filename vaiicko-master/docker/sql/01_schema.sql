-- vygenerovane ChatGPT na zaklade datoveho modelu
-- Adminer 5.4.1 MariaDB 12.1.2-MariaDB-ubu2404 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE TABLE `kurz` (
                        `id_kurz` int(10) unsigned NOT NULL AUTO_INCREMENT,
                        `nazov` varchar(100) NOT NULL,
                        `id_typ_kurzu` int(10) unsigned NOT NULL,
                        `id_obdobie` int(10) unsigned NOT NULL,
                        `popis` text DEFAULT NULL,
                        `cena` decimal(8,2) DEFAULT NULL,
                        `prihlasovanie_otvorene` tinyint(1) NOT NULL DEFAULT 0,
                        PRIMARY KEY (`id_kurz`),
                        KEY `fk_kurz_typ_kurzu` (`id_typ_kurzu`),
                        KEY `fk_kurz_obdobie` (`id_obdobie`),
                        CONSTRAINT `fk_kurz_obdobie` FOREIGN KEY (`id_obdobie`) REFERENCES `obdobie` (`id_obdobie`),
                        CONSTRAINT `fk_kurz_typ_kurzu` FOREIGN KEY (`id_typ_kurzu`) REFERENCES `typ_kurzu` (`id_typ_kurzu`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `obdobie` (
                           `id_obdobie` int(10) unsigned NOT NULL AUTO_INCREMENT,
                           `nazov` varchar(100) NOT NULL,
                           `datum_od` date NOT NULL,
                           `datum_do` date NOT NULL,
                           `popis` text DEFAULT NULL,
                           PRIMARY KEY (`id_obdobie`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `osoba` (
                         `id_osoba` int(10) unsigned NOT NULL AUTO_INCREMENT,
                         `id_pouzivatel` int(10) unsigned NOT NULL,
                         `meno` varchar(80) NOT NULL,
                         `priezvisko` varchar(80) NOT NULL,
                         `datum_narodenia` date NOT NULL,
                         `email` varchar(150) DEFAULT NULL,
                         `telefon` varchar(30) DEFAULT NULL,
                         `zastupca_meno` varchar(80) DEFAULT NULL,
                         `zastupca_priezvisko` varchar(80) DEFAULT NULL,
                         `zastupca_email` varchar(150) DEFAULT NULL,
                         `zastupca_telefon` varchar(30) DEFAULT NULL,
                         `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                         PRIMARY KEY (`id_osoba`),
                         KEY `fk_osoba_pouzivatel` (`id_pouzivatel`),
                         CONSTRAINT `fk_osoba_pouzivatel` FOREIGN KEY (`id_pouzivatel`) REFERENCES `pouzivatel` (`id_pouzivatel`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `osoba_skupina` (
                                 `id_osoba` int(10) unsigned NOT NULL,
                                 `id_skupina` int(10) unsigned NOT NULL,
                                 `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                 PRIMARY KEY (`id_osoba`,`id_skupina`),
                                 KEY `fk_osoba_skupina_skupina` (`id_skupina`),
                                 CONSTRAINT `fk_osoba_skupina_osoba` FOREIGN KEY (`id_osoba`) REFERENCES `osoba` (`id_osoba`) ON DELETE CASCADE,
                                 CONSTRAINT `fk_osoba_skupina_skupina` FOREIGN KEY (`id_skupina`) REFERENCES `skupina` (`id_skupina`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `pouzivatel` (
                              `id_pouzivatel` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `email` varchar(150) NOT NULL,
                              `password_hash` varchar(255) NOT NULL,
                              `rola` enum('admin','user') NOT NULL DEFAULT 'user',
                              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                              PRIMARY KEY (`id_pouzivatel`),
                              UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `prihlaska_kurz` (
                                  `id_prihlaska` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                  `id_osoba` int(10) unsigned NOT NULL,
                                  `id_kurz` int(10) unsigned NOT NULL,
                                  `stav` enum('nova','schvalena','zamietnuta','zrusena') NOT NULL DEFAULT 'nova',
                                  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                  `zastupca_meno` varchar(80) DEFAULT NULL,
                                  `zastupca_priezvisko` varchar(80) DEFAULT NULL,
                                  `zastupca_email` varchar(150) DEFAULT NULL,
                                  `zastupca_telefon` varchar(30) DEFAULT NULL,
                                  PRIMARY KEY (`id_prihlaska`),
                                  UNIQUE KEY `uq_osoba_kurz` (`id_osoba`,`id_kurz`),
                                  KEY `fk_prihlaska_kurz` (`id_kurz`),
                                  CONSTRAINT `fk_prihlaska_kurz` FOREIGN KEY (`id_kurz`) REFERENCES `kurz` (`id_kurz`) ON DELETE CASCADE,
                                  CONSTRAINT `fk_prihlaska_osoba` FOREIGN KEY (`id_osoba`) REFERENCES `osoba` (`id_osoba`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `prispevok` (
                             `id_prispevok` int(10) unsigned NOT NULL AUTO_INCREMENT,
                             `nazov` varchar(150) NOT NULL,
                             `obsah` text NOT NULL,
                             `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                             `viditelnost` enum('verejny','obdobie','skupina','udalost') NOT NULL,
                             `id_obdobie` int(10) unsigned DEFAULT NULL,
                             `id_skupina` int(10) unsigned DEFAULT NULL,
                             `id_udalost` int(10) unsigned DEFAULT NULL,
                             PRIMARY KEY (`id_prispevok`),
                             KEY `fk_prispevok_obdobie` (`id_obdobie`),
                             KEY `fk_prispevok_skupina` (`id_skupina`),
                             KEY `fk_prispevok_udalost` (`id_udalost`),
                             CONSTRAINT `fk_prispevok_obdobie` FOREIGN KEY (`id_obdobie`) REFERENCES `obdobie` (`id_obdobie`) ON DELETE SET NULL,
                             CONSTRAINT `fk_prispevok_skupina` FOREIGN KEY (`id_skupina`) REFERENCES `skupina` (`id_skupina`) ON DELETE SET NULL,
                             CONSTRAINT `fk_prispevok_udalost` FOREIGN KEY (`id_udalost`) REFERENCES `udalost` (`id_udalost`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `prispevok_subor` (
                                   `id_prispevok_subor` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                   `id_prispevok` int(10) unsigned NOT NULL,
                                   `original_name` varchar(255) NOT NULL,
                                   `stored_name` varchar(255) NOT NULL,
                                   `mime_type` varchar(100) NOT NULL,
                                   `size` int(10) unsigned NOT NULL,
                                   `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                   PRIMARY KEY (`id_prispevok_subor`),
                                   KEY `fk_prispevok_subor_prispevok` (`id_prispevok`),
                                   CONSTRAINT `fk_prispevok_subor_prispevok` FOREIGN KEY (`id_prispevok`) REFERENCES `prispevok` (`id_prispevok`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `skupina` (
                           `id_skupina` int(10) unsigned NOT NULL AUTO_INCREMENT,
                           `nazov` varchar(100) NOT NULL,
                           `id_obdobie` int(10) unsigned NOT NULL,
                           `popis` text DEFAULT NULL,
                           PRIMARY KEY (`id_skupina`),
                           KEY `fk_skupina_obdobie` (`id_obdobie`),
                           CONSTRAINT `fk_skupina_obdobie` FOREIGN KEY (`id_obdobie`) REFERENCES `obdobie` (`id_obdobie`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `typ_kurzu` (
                             `id_typ_kurzu` int(10) unsigned NOT NULL AUTO_INCREMENT,
                             `nazov` varchar(100) NOT NULL,
                             `popis` text DEFAULT NULL,
                             PRIMARY KEY (`id_typ_kurzu`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `udalost` (
                           `id_udalost` int(10) unsigned NOT NULL AUTO_INCREMENT,
                           `nazov` varchar(150) NOT NULL,
                           `typ` enum('trening','nacvik','vystupenie','ine') NOT NULL DEFAULT 'trening',
                           `zaciatok` datetime NOT NULL,
                           `koniec` datetime DEFAULT NULL,
                           `miesto` varchar(150) DEFAULT NULL,
                           `popis` text DEFAULT NULL,
                           `vyzaduje_reakciu` tinyint(1) NOT NULL DEFAULT 0,
                           `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                           `id_obdobie` int(10) unsigned NOT NULL,
                           PRIMARY KEY (`id_udalost`),
                           KEY `idx_udalost_zaciatok` (`zaciatok`),
                           KEY `idx_udalost_typ` (`typ`),
                           KEY `fk_udalost_obdobie` (`id_obdobie`),
                           CONSTRAINT `fk_udalost_obdobie` FOREIGN KEY (`id_obdobie`) REFERENCES `obdobie` (`id_obdobie`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `udalost_skupina` (
                                   `id_udalost` int(10) unsigned NOT NULL,
                                   `id_skupina` int(10) unsigned NOT NULL,
                                   PRIMARY KEY (`id_udalost`,`id_skupina`),
                                   KEY `fk_udalost_skupina_skupina` (`id_skupina`),
                                   CONSTRAINT `fk_udalost_skupina_skupina` FOREIGN KEY (`id_skupina`) REFERENCES `skupina` (`id_skupina`) ON DELETE CASCADE,
                                   CONSTRAINT `fk_udalost_skupina_udalost` FOREIGN KEY (`id_udalost`) REFERENCES `udalost` (`id_udalost`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `udalost_ucast` (
                                 `id_udalost` int(10) unsigned NOT NULL,
                                 `id_osoba` int(10) unsigned NOT NULL,
                                 `stav` enum('ucast','neucast') NOT NULL,
                                 `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                 PRIMARY KEY (`id_udalost`,`id_osoba`),
                                 KEY `fk_udalost_ucast_osoba` (`id_osoba`),
                                 CONSTRAINT `fk_udalost_ucast_osoba` FOREIGN KEY (`id_osoba`) REFERENCES `osoba` (`id_osoba`) ON DELETE CASCADE,
                                 CONSTRAINT `fk_udalost_ucast_udalost` FOREIGN KEY (`id_udalost`) REFERENCES `udalost` (`id_udalost`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


-- 2026-02-08 22:28:18 UTC