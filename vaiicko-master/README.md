Popis projektu
Tento projekt je semestrálna práca vytvorená v PHP pomocou dodaného MVC frameworku.
Aplikácia slúži na správu období, kurzov, skupín, rozvrhov, udalostí a prihlášok používateľov s rozlíšením rolí administrátor a používateľ.

Použité technológie

PHP 8
MVC architektúra
MariaDB
Docker, Docker Compose
HTML, CSS (Bootstrap)
JavaScript (AJAX – fetch)

Inštalácia a spustenie

Je potrebné mať nainštalovaný Docker a Docker Compose.

Postup

Stiahni alebo naklonuj projekt.

Prejdi do priečinka /docker.

Spusti aplikáciu príkazom:

docker-compose up

Pri prvom spustení sa automaticky vytvorí a naplní databáza.
Pri ďalších spusteniach stačí znova spustiť Docker.

Prístup k aplikácii

Aplikácia: http://localhost

Adminer (DB): http://localhost:8080

Docker konfigurácia
Súbor docker-compose.yml sa nachádza v priečinku /docker a mapuje projektový root cez ./../ do /var/www/html/.


Používateľské účty

Administrátor

email: admin@balet.sk
heslo: AdminHeslo123


Používateľ

email: user1@balet.sk
heslo: UserHeslo123

V databáze sa nachádzajú aj ďalšie testovacie účty.



Použitie umelej inteligencie
Pri vývoji aplikácie bol použitý nástroj umelej inteligencie (ChatGPT) ako podpora pri návrhu riešenia, refaktoringu kódu, kontrole chýb a písaní dokumentácie.
Vygenerovaný kód bol autorom podľa potreby manuálne upravený a integrovaný do výslednej aplikácie.
