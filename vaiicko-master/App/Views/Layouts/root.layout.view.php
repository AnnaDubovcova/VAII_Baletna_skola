<?php

/** @var string $contentHTML */
/** @var \Framework\Auth\AppUser $user */
/** @var \Framework\Support\LinkGenerator $link */

?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title><?= App\Configuration::APP_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $link->asset('favicons/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $link->asset('favicons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $link->asset('favicons/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= $link->asset('favicons/site.webmanifest') ?>">
    <link rel="shortcut icon" href="<?= $link->asset('favicons/favicon.ico') ?>">

    <!-- Bootstrap CSS -->
    <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
    >

    <!-- vlastné štýly -->
    <link rel="stylesheet" href="<?= $link->asset('css/styl.css') ?>">

</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= $link->url('home.index') ?>">
                <img src="<?= $link->asset('images/pointe_shoes.png') ?>"
                     title="<?= App\Configuration::APP_NAME ?>"
                     alt="Logo baletnej skoly"
                     style="height: 32px" class="me-2">
                <span><?= App\Configuration::APP_NAME ?></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $link->url('home.index') ?>">Domov</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $link->url('obdobie.index') ?>">Obdobia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $link->url('kurz.index') ?>">Kurzy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $link->url('typKurzu.index') ?>">Typy kurzov</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $link->url('skupina.index') ?>">Skupiny</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if ($user->isLoggedIn()) { ?>
                        <li class="nav-item">
                            <span class="navbar-text me-3">
                                Prihlásený: <b><?= htmlspecialchars($user->getName()) ?></b>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $link->url('auth.logout') ?>">Odhlásiť</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= App\Configuration::LOGIN_URL ?>">Prihlásiť</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="py-4">
    <div class="container">
        <div class="web-content">
            <?= $contentHTML ?>
        </div>
    </div>
</main>

<footer class="border-top mt-4 py-3">
    <div class="container text-center text-muted small">
        &copy; <?= date('Y') ?> <?= App\Configuration::APP_NAME ?> – Tanečná škola
    </div>
</footer>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

<!-- Tvoj vlastný skript -->
<script src="<?= $link->asset('js/script.js') ?>"></script>

</body>
</html>
