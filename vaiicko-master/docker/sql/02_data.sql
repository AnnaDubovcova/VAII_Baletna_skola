--vygenerovane AI
-- 02_data.sql
-- Adminer 5.4.1 MariaDB 12.1.2-MariaDB-ubu2404 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

INSERT INTO `kurz` (`id_kurz`, `nazov`, `id_typ_kurzu`, `id_obdobie`, `popis`, `cena`, `prihlasovanie_otvorene`) VALUES
                                                                                                                     (10,	'Moderný tanec – mierne pokročilí',	2,	19,	'Kurz pre tých, čo už poznajú základné prvky moderného tanca.',	140.00,	1),
                                                                                                                     (14,	'blabla',	3,	16,	NULL,	NULL,	1),
                                                                                                                     (17,	'Zaklady baletu pre najmensich',	3,	1,	'Rozvoj pohybovych schopnosti u najsmenisch deti. Vhodne pre deti od 3 do 6 rokov.',	135.00,	1),
                                                                                                                     (18,	'Zaciatocnici - dospeli',	1,	16,	NULL,	145.00,	1);

INSERT INTO `obdobie` (`id_obdobie`, `nazov`, `datum_od`, `datum_do`, `popis`) VALUES
                                                                                   (1,	'Leto 2025',	'2025-07-01',	'2025-08-31',	'Letné intenzívne kurzy'),
                                                                                   (16,	'Leto 2026',	'2026-01-05',	'2026-06-30',	'Letny semester'),
                                                                                   (19,	'Jeseň 2025',	'2025-09-01',	'2025-12-20',	'Štandardný zimný semester');

INSERT INTO `osoba` (`id_osoba`, `id_pouzivatel`, `meno`, `priezvisko`, `datum_narodenia`, `email`, `telefon`, `zastupca_meno`, `zastupca_priezvisko`, `zastupca_email`, `zastupca_telefon`, `created_at`) VALUES
                                                                                                                                                                                                               (1,	2,	'Ana',	'Dub',	'2010-10-10',	NULL,	NULL,	'jan',	'dubik',	NULL,	'0902222222',	'2026-01-20 11:59:13'),
                                                                                                                                                                                                               (3,	2,	'Aa',	'Dub',	'2005-01-11',	'kontakt@balet.sk',	NULL,	NULL,	NULL,	NULL,	NULL,	'2026-01-20 13:04:05'),
                                                                                                                                                                                                               (4,	2,	'Misko',	'Dub',	'2010-02-12',	NULL,	NULL,	'adam',	'dub',	'adam@dub.sk',	NULL,	'2026-01-21 09:38:47');


INSERT INTO `pouzivatel` (`id_pouzivatel`, `email`, `password_hash`, `rola`, `created_at`) VALUES
                                                                                               (1,	'admin@balet.sk',	'$2y$10$oqfoC8.h1V6LJ5P1tHUMROKxfcoejTUp4PaoLQdmsR/h0/GxD33r.',	'admin',	'2026-01-19 20:12:17'),
                                                                                               (2,	'user1@balet.sk',	'$2y$10$tRZTEJxDcB/3ez.tKYugt.2Md13iTmCvWxE1QIKCAB5E2/rhc0bM6',	'user',	'2026-01-19 21:49:11');

INSERT INTO `prihlaska_kurz` (`id_prihlaska`, `id_osoba`, `id_kurz`, `stav`, `created_at`, `zastupca_meno`, `zastupca_priezvisko`, `zastupca_email`, `zastupca_telefon`) VALUES
                                                                                                                                                                             (1,	1,	10,	'zamietnuta',	'2026-01-20 18:12:05',	'jan',	'dubik',	NULL,	'0902222222'),
                                                                                                                                                                             (3,	3,	10,	'schvalena',	'2026-01-20 19:28:22',	NULL,	NULL,	NULL,	NULL),
                                                                                                                                                                             (5,	3,	14,	'schvalena',	'2026-01-20 22:01:37',	NULL,	NULL,	NULL,	NULL),
                                                                                                                                                                             (9,	1,	14,	'schvalena',	'2026-01-20 22:03:15',	'jan',	'dubik',	NULL,	'0902222222'),
                                                                                                                                                                             (13,	3,	17,	'nova',	'2026-01-21 09:38:53',	NULL,	NULL,	NULL,	NULL),
                                                                                                                                                                             (16,	4,	14,	'schvalena',	'2026-01-21 09:39:07',	'adam',	'dub',	'adam@dub.sk',	NULL),
                                                                                                                                                                             (17,	4,	17,	'schvalena',	'2026-01-21 09:39:08',	'adam',	'dub',	'adam@dub.sk',	NULL),
                                                                                                                                                                             (18,	4,	10,	'zamietnuta',	'2026-01-21 09:39:11',	'adam',	'dub',	'adam@dub.sk',	NULL);

INSERT INTO `skupina` (`id_skupina`, `nazov`, `id_obdobie`, `popis`) VALUES
                                                                         (2,	'Labutky',	1,	NULL),
                                                                         (3,	'skupinka',	16,	'toto je skupinka'),
                                                                         (4,	'skupina',	1,	'v tejto skupine');

INSERT INTO `typ_kurzu` (`id_typ_kurzu`, `nazov`, `popis`) VALUES
                                                               (1,	'Klasický balet',	'Základy klasického baletu'),
                                                               (2,	'Moderný tanec',	'Moderné a súčasné tanečné techniky'),
                                                               (3,	'Detský kurz',	'Hrave tanečné lekcie pre deti'),
                                                               (4,	'Prípravka na vystúpenia',	'Intenzívnejšia príprava na vystúpenia');

INSERT INTO `udalost` (`id_udalost`, `nazov`, `typ`, `zaciatok`, `koniec`, `miesto`, `popis`, `created_at`) VALUES
    (1,	'Generalna skuska',	'nacvik',	'2026-01-22 17:43:00',	'2026-01-22 19:43:00',	NULL,	NULL,	'2026-01-21 16:44:31');


-- 2026-02-02 10:56:51 UTC
