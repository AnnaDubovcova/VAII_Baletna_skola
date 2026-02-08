-- Adminer 5.4.1 MariaDB 12.1.2-MariaDB-ubu2404 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

INSERT INTO `kurz` (`id_kurz`, `nazov`, `id_typ_kurzu`, `id_obdobie`, `popis`, `cena`, `prihlasovanie_otvorene`) VALUES
                                                                                                                     (14,	'blabla',	3,	16,	NULL,	NULL,	1),
                                                                                                                     (17,	'Zaklady baletu pre najmensich',	3,	1,	'Rozvoj pohybovych schopnosti u najsmenisch deti. Vhodne pre deti od 3 do 6 rokov.',	135.00,	1),
                                                                                                                     (18,	'Zaciatocnici - dospeli',	1,	16,	NULL,	145.00,	1),
                                                                                                                     (19,	'Základy baletu pre  najmenších',	3,	20,	NULL,	135.00,	1),
                                                                                                                     (20,	'Deti nad 7r.',	3,	1,	NULL,	145.00,	1),
                                                                                                                     (21,	'Základy klasického baletu',	7,	20,	NULL,	135.00,	1);

INSERT INTO `obdobie` (`id_obdobie`, `nazov`, `datum_od`, `datum_do`, `popis`) VALUES
                                                                                   (1,	'Leto 2025',	'2025-07-01',	'2025-08-31',	'Letné intenzívne kurzy'),
                                                                                   (16,	'Leto 2026',	'2026-01-05',	'2026-06-30',	'Letny semester'),
                                                                                   (20,	'Zima 2026',	'2026-09-01',	'2027-01-29',	'Obdobie  štandardného zimného polroka 2026');

INSERT INTO `osoba` (`id_osoba`, `id_pouzivatel`, `meno`, `priezvisko`, `datum_narodenia`, `email`, `telefon`, `zastupca_meno`, `zastupca_priezvisko`, `zastupca_email`, `zastupca_telefon`, `created_at`) VALUES
                                                                                                                                                                                                               (1,	2,	'Ana',	'Dub',	'2010-10-10',	NULL,	NULL,	'jan',	'dubik',	NULL,	'0902222222',	'2026-01-20 11:59:13'),
                                                                                                                                                                                                               (3,	2,	'Aa',	'Dub',	'2005-01-11',	'kontakt@balet.sk',	NULL,	NULL,	NULL,	NULL,	NULL,	'2026-01-20 13:04:05'),
                                                                                                                                                                                                               (4,	2,	'Misko',	'Dub',	'2010-02-12',	NULL,	NULL,	'adam',	'dub',	'adam@dub.sk',	NULL,	'2026-01-21 09:38:47'),
                                                                                                                                                                                                               (5,	3,	'Janka',	'Hrašková',	'2020-02-02',	NULL,	NULL,	'Ján',	'Hraško',	'user2@balet.sk',	NULL,	'2026-02-08 11:04:49');

INSERT INTO `osoba_skupina` (`id_osoba`, `id_skupina`, `created_at`) VALUES
                                                                         (1,	2,	'2026-02-05 22:45:14'),
                                                                         (1,	6,	'2026-02-06 12:18:18'),
                                                                         (3,	2,	'2026-02-05 22:45:12'),
                                                                         (4,	4,	'2026-02-08 21:49:39'),
                                                                         (4,	6,	'2026-02-08 20:19:00');

INSERT INTO `pouzivatel` (`id_pouzivatel`, `email`, `password_hash`, `rola`, `created_at`) VALUES
                                                                                               (1,	'admin@balet.sk',	'$2y$10$oqfoC8.h1V6LJ5P1tHUMROKxfcoejTUp4PaoLQdmsR/h0/GxD33r.',	'admin',	'2026-01-19 20:12:17'),
                                                                                               (2,	'user1@balet.sk',	'$2y$10$tRZTEJxDcB/3ez.tKYugt.2Md13iTmCvWxE1QIKCAB5E2/rhc0bM6',	'user',	'2026-01-19 21:49:11'),
                                                                                               (3,	'user2@balet.sk',	'$2y$10$J0CGPWMDmOJnr5mvnmKMCOm6YztufsVz/.vELlBgyDaFVXdSQJc.W',	'user',	'2026-02-08 10:52:30'),
                                                                                               (4,	'user3@balet.sk',	'$2y$10$H/KfWf.D8pNrwlJ0gx5qauv9lQZOTizYGRCmXG9BNMwwgy0XPCIyi',	'user',	'2026-02-08 22:16:36');

INSERT INTO `prihlaska_kurz` (`id_prihlaska`, `id_osoba`, `id_kurz`, `stav`, `created_at`, `zastupca_meno`, `zastupca_priezvisko`, `zastupca_email`, `zastupca_telefon`) VALUES
                                                                                                                                                                             (5,	3,	14,	'schvalena',	'2026-01-20 22:01:37',	NULL,	NULL,	NULL,	NULL),
                                                                                                                                                                             (9,	1,	14,	'schvalena',	'2026-01-20 22:03:15',	'jan',	'dubik',	NULL,	'0902222222'),
                                                                                                                                                                             (13,	3,	17,	'zamietnuta',	'2026-01-21 09:38:53',	NULL,	NULL,	NULL,	NULL),
                                                                                                                                                                             (16,	4,	14,	'schvalena',	'2026-01-21 09:39:07',	'adam',	'dub',	'adam@dub.sk',	NULL),
                                                                                                                                                                             (17,	4,	17,	'schvalena',	'2026-01-21 09:39:08',	'adam',	'dub',	'adam@dub.sk',	NULL),
                                                                                                                                                                             (19,	4,	18,	'zamietnuta',	'2026-02-06 21:03:11',	'adam',	'dub',	'adam@dub.sk',	NULL),
                                                                                                                                                                             (20,	5,	17,	'schvalena',	'2026-02-08 11:06:01',	'Ján',	'Hraško',	'user2@balet.sk',	NULL),
                                                                                                                                                                             (21,	5,	18,	'zamietnuta',	'2026-02-08 20:05:22',	'Ján',	'Hraško',	'user2@balet.sk',	NULL),
                                                                                                                                                                             (22,	5,	14,	'zamietnuta',	'2026-02-08 20:10:01',	'Ján',	'Hraško',	'user2@balet.sk',	NULL),
                                                                                                                                                                             (23,	5,	20,	'nova',	'2026-02-08 20:21:18',	'Ján',	'Hraško',	'user2@balet.sk',	NULL),
                                                                                                                                                                             (24,	5,	19,	'schvalena',	'2026-02-08 20:21:20',	'Ján',	'Hraško',	'user2@balet.sk',	NULL);

INSERT INTO `prispevok` (`id_prispevok`, `nazov`, `obsah`, `created_at`, `viditelnost`, `id_obdobie`, `id_skupina`, `id_udalost`) VALUES
                                                                                                                                      (1,	'Príspevok',	'toto je prvy prispevok',	'2026-02-07 11:20:25',	'verejny',	NULL,	NULL,	NULL),
                                                                                                                                      (2,	'pripevok pre obdobie aktivne',	'cauko',	'2026-02-07 11:22:11',	'obdobie',	16,	NULL,	NULL),
                                                                                                                                      (3,	'úrispevom jesen 1',	'obsah pripevku ide sem',	'2026-02-07 11:22:43',	'obdobie',	NULL,	NULL,	NULL),
                                                                                                                                      (4,	'Nové predstavenie',	'Pripravujeme nové predtsavenie Labutie Jazero',	'2026-02-07 11:59:58',	'obdobie',	1,	NULL,	NULL),
                                                                                                                                      (5,	'Prispevok 1',	'toto je obsah prispevku pre skupinu mini',	'2026-02-07 15:55:45',	'skupina',	NULL,	6,	NULL),
                                                                                                                                      (6,	'pripevok',	'toto  je prispevok  leto 2025 deti mini',	'2026-02-07 15:56:34',	'skupina',	NULL,	NULL,	NULL),
                                                                                                                                      (7,	'prispevok pre udalost pondelkovy trening',	'kto ma,  prineste si kostym z  minuleho predtsvania',	'2026-02-07 15:57:24',	'udalost',	NULL,	NULL,	2),
                                                                                                                                      (8,	'prispevok pre akciu',	'caute snad sa vidime, tesim sa',	'2026-02-07 16:11:08',	'udalost',	NULL,	NULL,	4);

INSERT INTO `prispevok_subor` (`id_prispevok_subor`, `id_prispevok`, `original_name`, `stored_name`, `mime_type`, `size`, `created_at`) VALUES
                                                                                                                                            (1,	2,	'Screenshot 2026-02-08 023310.png',	'2/09ccd49439fe74c058b33777b624aa2b.png',	'image/png',	57042,	'2026-02-08 01:33:26'),
                                                                                                                                            (7,	1,	'pointe.png',	'26afeaaf290adcf549b71b852c05eb6b.png',	'image/png',	11120,	'2026-02-08 21:37:28');

INSERT INTO `skupina` (`id_skupina`, `nazov`, `id_obdobie`, `popis`) VALUES
                                                                         (2,	'Labutky',	1,	NULL),
                                                                         (4,	'skupina',	1,	'v tejto skupine'),
                                                                         (6,	'mini',	16,	NULL);

INSERT INTO `typ_kurzu` (`id_typ_kurzu`, `nazov`, `popis`) VALUES
                                                               (1,	'Klasický balet',	'Základy klasického baletu'),
                                                               (3,	'Detský kurz',	'Hrave tanečné lekcie pre deti'),
                                                               (4,	'Prípravka na vystúpenia',	'Intenzívnejšia príprava na vystúpenia'),
                                                               (7,	'Kurz pre dospelých',	NULL);

INSERT INTO `udalost` (`id_udalost`, `nazov`, `typ`, `zaciatok`, `koniec`, `miesto`, `popis`, `vyzaduje_reakciu`, `created_at`, `id_obdobie`) VALUES
                                                                                                                                                  (1,	'Generalna skuska',	'nacvik',	'2026-01-22 17:43:00',	'2026-01-22 19:43:00',	NULL,	NULL,	0,	'2026-01-21 16:44:31',	1),
                                                                                                                                                  (2,	'Pondelkovy  tr',	'trening',	'2026-02-04 08:44:00',	'2026-02-05 01:44:00',	NULL,	NULL,	0,	'2026-02-04 21:44:54',	1),
                                                                                                                                                  (4,	'akcia',	'trening',	'2026-02-06 15:30:00',	'2026-02-06 16:35:00',	'tu',	'nieco sa dejetu  a teraz zas',	0,	'2026-02-06 12:29:09',	16),
                                                                                                                                                  (5,	'vystupeenie',	'trening',	'2026-02-13 22:11:00',	'2026-02-08 22:11:00',	NULL,	NULL,	1,	'2026-02-07 21:11:52',	16);

INSERT INTO `udalost_skupina` (`id_udalost`, `id_skupina`) VALUES
                                                               (1,	2),
                                                               (4,	6),
                                                               (5,	6);

INSERT INTO `udalost_ucast` (`id_udalost`, `id_osoba`, `stav`, `updated_at`) VALUES
                                                                                 (4,	1,	'neucast',	'2026-02-07 23:19:47'),
                                                                                 (5,	1,	'ucast',	'2026-02-07 21:15:40');

-- 2026-02-08 22:29:17 UTC