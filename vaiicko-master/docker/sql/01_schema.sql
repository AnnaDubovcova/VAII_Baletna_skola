-- vygenerovane ChatGPT na zaklade datoveho modelu
-- 1) Typ kurzu
CREATE TABLE typ_kurzu (
                           id_typ_kurzu INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                           nazov        VARCHAR(100) NOT NULL,
                           popis        TEXT NULL
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 2) Obdobie (napr. Leto 2025)
CREATE TABLE obdobie (
                         id_obdobie INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                         nazov      VARCHAR(100) NOT NULL,
                         datum_od   DATE NOT NULL,
                         datum_do   DATE NOT NULL,
                         popis      TEXT NULL
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 3) Kurz
CREATE TABLE kurz (
                      id_kurz                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                      nazov                   VARCHAR(100) NOT NULL,
                      id_typ_kurzu            INT UNSIGNED NOT NULL,
                      id_obdobie              INT UNSIGNED NOT NULL,
                      popis                   TEXT NULL,
                      cena                    DECIMAL(8,2) NULL,
                      prihlasovanie_otvorene  TINYINT(1) NOT NULL DEFAULT 0,  -- 0 = zatvorené, 1 = otvorené

                      CONSTRAINT fk_kurz_typ_kurzu
                          FOREIGN KEY (id_typ_kurzu) REFERENCES typ_kurzu(id_typ_kurzu),

                      CONSTRAINT fk_kurz_obdobie
                          FOREIGN KEY (id_obdobie) REFERENCES obdobie(id_obdobie)
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 4) Skupina (viaže sa na obdobie)
CREATE TABLE skupina (
                         id_skupina  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                         nazov       VARCHAR(100) NOT NULL,
                         id_obdobie  INT UNSIGNED NOT NULL,
                         popis       TEXT NULL,

                         CONSTRAINT fk_skupina_obdobie
                             FOREIGN KEY (id_obdobie) REFERENCES obdobie(id_obdobie)
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4;
