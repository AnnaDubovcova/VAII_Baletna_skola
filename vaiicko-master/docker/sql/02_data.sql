--vygenerovane AI
-- 02_data.sql

-- Typy kurzov
INSERT INTO typ_kurzu (nazov, popis) VALUES
                                         ('Klasický balet', 'Základy klasického baletu'),
                                         ('Moderný tanec', 'Moderné a súčasné tanečné techniky'),
                                         ('Detský kurz', 'Hrave tanečné lekcie pre deti'),
                                         ('Prípravka na vystúpenia', 'Intenzívnejšia príprava na vystúpenia');

-- Obdobia
INSERT INTO obdobie (nazov, datum_od, datum_do, popis) VALUES
                                                           ('Leto 2025',  '2025-07-01', '2025-08-31', 'Letné intenzívne kurzy'),
                                                           ('Jeseň 2025', '2025-09-01', '2025-12-20', 'Štandardný zimný semester');

-- Kurzy (predpoklad: auto_increment ID začínajú od 1)
-- typ_kurzu: 1 = Klasický balet, 2 = Moderný tanec, 3 = Detský kurz, 4 = Prípravka
-- obdobie:   1 = Leto 2025, 2 = Jeseň 2025

INSERT INTO kurz (nazov, id_typ_kurzu, id_obdobie, popis, kapacita, cena, prihlasovanie_otvorene) VALUES
                                                                                                      ('Balet pre začiatočníkov', 1, 2,
                                                                                                       'Základná technika baletu pre úplných začiatočníkov.',
                                                                                                       120.00, 1),
                                                                                                      ('Moderný tanec – mierne pokročilí', 2, 2,
                                                                                                       'Kurz pre tých, čo už poznajú základné prvky moderného tanca.',
                                                                                                        140.00, 1),
                                                                                                      ('Letný detský tábor – tanec', 3, 1,
                                                                                                       'Denný letný tábor pre deti so zameraním na tanec a pohybové hry.',
                                                                                                        180.00, 0),
                                                                                                      ('Príprava na vianočné vystúpenie', 4, 2,
                                                                                                       'Intenzívny kurz pre vybraných študentov.',
                                                                                                       200.00, 0);

-- Skupiny (viazané na obdobia)
INSERT INTO skupina (nazov, id_obdobie, popis) VALUES
                                                              ('Skupina A – mladší žiaci', 2, 'Žiaci približne 6–9 rokov'),
                                                              ('Skupina B – starší žiaci', 2, 'Žiaci približne 10–14 rokov'),
                                                              ('Letná skupina – mix',     1, 'Zmiešaná skupina účastníkov letného tábora');
