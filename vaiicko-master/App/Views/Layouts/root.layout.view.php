

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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <!-- Tvoje vlastné štýly -->
    <link rel="stylesheet" href="<?= $link->asset('css/styl.css') ?>">

</head>
<body>

<header>
    <!-- Horný panel -->
    <nav class="navbar navbar-light bg-white border-bottom">
        <div class="container-fluid">

            <!-- Ľavá strana: hamburger + logo -->
            <div class="d-flex align-items-center">
                <!-- Hamburger pre sidebar (viditeľný hlavne na mobile) -->
                <button class="btn btn-outline-secondary me-2 d-md-none"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#sidebarMenu"
                        aria-controls="sidebarMenu"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                    ☰
                </button>

                <a class="navbar-brand d-flex align-items-center" href="<?= $link->url('home.index') ?>">
                    <img src="<?= $link->asset('images/pointe_shoes.png') ?>"
                         title="<?= App\Configuration::APP_NAME ?>"
                         alt="Framework Logo"
                         style="height: 32px" class="me-2">
                    <span><?= App\Configuration::APP_NAME ?></span>
                </a>
            </div>

            <!-- Pravá strana: notifikácie / login -->
            <div class="d-flex align-items-center">



                <!-- Pravá strana: notifikácie / login -->
                <div class="d-flex align-items-center">

                    <?php if ($user->isLoggedIn()) { ?>
                        <!-- Notification icon -->
                        <a href="#" class="nav-link me-3 text-secondary">
                            <i class="bi bi-bell" style="font-size: 1.3rem;"></i>
                        </a>

                        <!-- Logged in user -->
                        <span class="navbar-text me-3">Prihlásený: <b><?= htmlspecialchars($user->getName()) ?></b></span>

                        <a class="nav-link" href="<?= $link->url('auth.logout') ?>">Odhlásiť</a>

                    <?php } else { ?>
                        <!-- Login for guests -->
                        <a class="nav-link" href="<?= App\Configuration::LOGIN_URL ?>">Prihlásiť</a>
                    <?php } ?>

                </div>

        </div>
    </nav>
</header>

<div class="container-fluid main-shell">
    <div class="row">


        <!-- Ľavý sidebar -->
        <nav id="sidebarMenu"
             class="col-12 col-md-3 col-lg-2 bg-light sidebar collapse d-md-block">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column sidebar-nav">
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
                    <?php if ($this->user->isLoggedIn() && !$this->user->isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $link->url('osoba.index') ?>">Moje osoby</a>
                        </li>
                    <?php endif; ?>

                    <!-- neskôr môžeš pridať ďalšie sekcie -->
                </ul>
            </div>
        </nav>

        <!-- Hlavný obsah -->
        <main class="col-12 col-md-9 col-lg-10 py-4 main-content">
            <div class="container-fluid">
                <div class="web-content">
                    <?= $contentHTML ?>
                </div>
            </div>
        </main>
    </div>
</div>

<footer class="border-top mt-4 py-3">
    <div class="container text-center text-muted small">
        &copy; <?= date('Y') ?> <?= App\Configuration::APP_NAME ?> – Baletná škola
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
