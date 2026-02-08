//alert('JS pre obdobie-form je načítaný');


document.addEventListener('DOMContentLoaded', function () {
    //alert('JS pre obdobie-form je načítaný');

    // ---------- Shared helpers ----------
    function findContainer(input) {
        var el = input.parentNode;
        while (el && el !== document.body) {
            if (el.classList && el.classList.contains('mb-3')) {
                return el;
            }
            el = el.parentNode;
        }
        return null;
    }

    function clearError(input) {
        if (!input) return;

        input.classList.remove('is-invalid');

        var container = findContainer(input);
        if (!container) return;

        var feedbacks = container.querySelectorAll('.invalid-feedback.js-client');
        for (var i = 0; i < feedbacks.length; i++) {
            feedbacks[i].parentNode.removeChild(feedbacks[i]);
        }
    }

    function showError(input, message) {
        if (!input) return;

        input.classList.add('is-invalid');

        var container = findContainer(input);
        if (!container) return;

        var feedback = document.createElement('div');
        feedback.className = 'invalid-feedback js-client';
        feedback.textContent = message;

        container.appendChild(feedback);
    }

    function isValidEmail(val) {
        // jednoduchý, ale postačujúci pattern
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
    }

    // ---------- Obdobie form ----------
    (function initObdobieForm() {
        var form = document.getElementById('obdobie-form');
        if (!form) return;

        var nazov = document.getElementById('nazov');
        var datumOd = document.getElementById('datum_od');
        var datumDo = document.getElementById('datum_do');

        form.addEventListener('submit', function (event) {
            var valid = true;

            clearError(nazov);
            clearError(datumOd);
            clearError(datumDo);

            if (!nazov.value.trim()) {
                showError(nazov, 'Názov je povinný.');
                valid = false;
            } else if (nazov.value.length > 100) {
                showError(nazov, 'Názov môže mať max. 100 znakov.');
                valid = false;
            }

            if (!datumOd.value) {
                showError(datumOd, 'Dátum od je povinný.');
                valid = false;
            }

            if (!datumDo.value) {
                showError(datumDo, 'Dátum do je povinný.');
                valid = false;
            }

            if (datumOd.value && datumDo.value) {
                var od = new Date(datumOd.value);
                var doD = new Date(datumDo.value);
                if (doD < od) {
                    showError(datumDo, 'Dátum do musí byť po dátume od.');
                    valid = false;
                }
            }

            if (!valid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    })();


    // ---------- Osoba form ----------
    (function initOsobaForm() {
        var form = document.getElementById('osoba-form');
        if (!form) return;

        console.log('OSOBA form JS aktivny', form);


        var meno = document.getElementById('meno');
        var priezvisko = document.getElementById('priezvisko');
        var datumNarodenia = document.getElementById('datum_narodenia');

        var email = document.getElementById('email');
        var telefon = document.getElementById('telefon');

        var zastMeno = document.getElementById('zastupca_meno');
        var zastPriezvisko = document.getElementById('zastupca_priezvisko');
        var zastEmail = document.getElementById('zastupca_email');
        var zastTelefon = document.getElementById('zastupca_telefon');

        form.addEventListener('submit', function (event) {
            console.log('OSOBA submit zachyteny');


            var valid = true;

            // clear
            clearError(meno);
            clearError(priezvisko);
            clearError(datumNarodenia);
            clearError(email);
            clearError(telefon);
            clearError(zastMeno);
            clearError(zastPriezvisko);
            clearError(zastEmail);
            clearError(zastTelefon);

            // povinné: meno
            if (!meno.value.trim()) {
                showError(meno, 'Meno je povinné.');
                valid = false;
            } else if (meno.value.length > 80) {
                showError(meno, 'Meno môže mať max. 80 znakov.');
                valid = false;
            }

            // povinné: priezvisko
            if (!priezvisko.value.trim()) {
                showError(priezvisko, 'Priezvisko je povinné.');
                valid = false;
            } else if (priezvisko.value.length > 80) {
                showError(priezvisko, 'Priezvisko môže mať max. 80 znakov.');
                valid = false;
            }

            // povinné: dátum narodenia (+ nie v budúcnosti)
            if (!datumNarodenia.value) {
                showError(datumNarodenia, 'Dátum narodenia je povinný.');
                valid = false;
            } else {
                var dn = new Date(datumNarodenia.value);
                var today = new Date();
                today.setHours(0, 0, 0, 0);
                if (dn > today) {
                    showError(datumNarodenia, 'Dátum narodenia nemôže byť v budúcnosti.');
                    valid = false;
                }
            }

            // voliteľné: email (ak vyplnené -> validovať)
            if (email && email.value.trim() && !isValidEmail(email.value.trim())) {
                showError(email, 'Zadaj platný email.');
                valid = false;
            }

            // voliteľné: telefón (ak vyplnené -> aspoň 6 znakov)
            if (telefon && telefon.value.trim() && telefon.value.trim().length < 6) {
                showError(telefon, 'Telefón vyzerá príliš krátky.');
                valid = false;
            }

            // zástupca: ak je vyplnené niečo, vyžaduj aspoň meno+priezvisko
            var anyZast =
                (zastMeno && zastMeno.value.trim()) ||
                (zastPriezvisko && zastPriezvisko.value.trim()) ||
                (zastEmail && zastEmail.value.trim()) ||
                (zastTelefon && zastTelefon.value.trim());

            if (anyZast) {
                if (zastMeno && !zastMeno.value.trim()) {
                    showError(zastMeno, 'Doplň meno zákonného zástupcu.');
                    valid = false;
                }
                if (zastPriezvisko && !zastPriezvisko.value.trim()) {
                    showError(zastPriezvisko, 'Doplň priezvisko zákonného zástupcu.');
                    valid = false;
                }
                if (zastEmail && zastEmail.value.trim() && !isValidEmail(zastEmail.value.trim())) {
                    showError(zastEmail, 'Zadaj platný email zákonného zástupcu.');
                    valid = false;
                }
                if (zastTelefon && zastTelefon.value.trim() && zastTelefon.value.trim().length < 6) {
                    showError(zastTelefon, 'Telefón zákonného zástupcu vyzerá príliš krátky.');
                    valid = false;
                }
            }

            console.log('OSOBA valid?', valid);


            if (!valid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    })();

    // ---------- Kurz form ----------
    (function initKurzForm() {
        var form = document.getElementById('kurz-form');
        if (!form) return;

        var nazov = form.querySelector('#nazov');
        var idTyp = form.querySelector('#id_typ_kurzu');
        var idObdobie = form.querySelector('#id_obdobie');
        var cena = form.querySelector('#cena');
        var popis = form.querySelector('#popis');

        form.addEventListener('submit', function (event) {
            var valid = true;

            clearError(nazov);
            clearError(idTyp);
            clearError(idObdobie);
            clearError(cena);
            clearError(popis);

            if (!nazov.value.trim()) {
                showError(nazov, 'Názov je povinný.');
                valid = false;
            } else if (nazov.value.length > 100) {
                showError(nazov, 'Názov môže mať max. 100 znakov.');
                valid = false;
            }

            if (!idTyp.value) {
                showError(idTyp, 'Vyber typ kurzu.');
                valid = false;
            }

            if (!idObdobie.value) {
                showError(idObdobie, 'Vyber obdobie.');
                valid = false;
            }

            if (cena && cena.value.trim()) {
                // povolíme aj čiarku, premeníme na bodku
                var raw = cena.value.trim().replace(',', '.');

                // číslo s max 2 desatinnými miestami
                if (!/^\d+(\.\d{1,2})?$/.test(raw)) {
                    showError(cena, 'Cena musí byť číslo (napr. 120.00).');
                    valid = false;
                } else {
                    var num = parseFloat(raw);
                    if (isNaN(num) || num < 0) {
                        showError(cena, 'Cena nemôže byť záporná.');
                        valid = false;
                    }
                }
            }

            if (popis && popis.value.length > 1000) {
                showError(popis, 'Popis môže mať max. 1000 znakov.');
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    })();


// ---------- Skupina form ----------
    (function initSkupinaForm() {
        var form = document.getElementById('skupina-form');
        if (!form) return;

        var nazov = form.querySelector('#nazov');
        var idObdobie = form.querySelector('#id_obdobie');
        var popis = form.querySelector('#popis');

        form.addEventListener('submit', function (event) {
            var valid = true;

            clearError(nazov);
            clearError(idObdobie);
            clearError(popis);

            if (!nazov.value.trim()) {
                showError(nazov, 'Názov je povinný.');
                valid = false;
            } else if (nazov.value.length > 100) {
                showError(nazov, 'Názov môže mať max. 100 znakov.');
                valid = false;
            }

            if (!idObdobie.value) {
                showError(idObdobie, 'Vyber obdobie.');
                valid = false;
            }

            if (popis && popis.value.length > 1000) {
                showError(popis, 'Popis môže mať max. 1000 znakov.');
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    })();


// ---------- Typ kurzu form ----------
    (function initTypKurzuForm() {
        var form = document.getElementById('typKurzu-form');
        if (!form) return;

        var nazov = form.querySelector('#nazov');
        var popis = form.querySelector('#popis');

        form.addEventListener('submit', function (event) {
            var valid = true;

            clearError(nazov);
            clearError(popis);

            if (!nazov.value.trim()) {
                showError(nazov, 'Názov je povinný.');
                valid = false;
            } else if (nazov.value.length > 100) {
                showError(nazov, 'Názov môže mať max. 100 znakov.');
                valid = false;
            }

            if (popis && popis.value.length > 1000) {
                showError(popis, 'Popis môže mať max. 1000 znakov.');
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    })();

    (function initAdminPrihlaskaAjax() {
        const form = document.getElementById('admin-prihlaska-filter');
        const tbody = document.getElementById('admin-prihlasky-body');

        // Tento JS má zmysel len na admin stránke prihlášok
        if (!form || !tbody) return;

        // Zabraňuje posielaniu viacerých requestov naraz (double click / rýchle zmeny filtra)
        let busy = false;

        /**
         * Vráti Bootstrap triedu pre badge podľa stavu prihlášky.
         */
        function badgeClassFor(stav) {
            switch (stav) {
                case 'nova': return 'bg-secondary';
                case 'schvalena': return 'bg-success';
                case 'zamietnuta': return 'bg-danger';
                case 'zrusena': return 'bg-warning';
                default: return 'bg-secondary';
            }
        }

        /**
         * Jednoduchý HTML escape pre bezpečné vloženie textu do DOM cez template string.
         * (XSS ochrana pri dynamickom renderovaní)
         */
        function escapeHtml(str) {
            return String(str ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        /**
         * Načíta JSON odpoveď bezpečným spôsobom.
         * - kontroluje res.ok
         * - ak server vráti HTML (napr. error page), vypíše použiteľnú chybu
         */
        async function fetchJson(url, options = {}) {
            const res = await fetch(url, {
                ...options,
                headers: {
                    'Accept': 'application/json',
                    ...(options.headers || {})
                }
            });

            if (!res.ok) {
                // Skúsime získať text odpovede (môže to byť HTML error page)
                const text = await res.text().catch(() => '');
                throw new Error(`HTTP ${res.status} ${res.statusText} ${text ? '- ' + text.slice(0, 120) : ''}`);
            }

            // Ak odpoveď nie je JSON, res.json() vyhodí chybu -> ošetríme
            try {
                return await res.json();
            } catch (e) {
                const text = await res.text().catch(() => '');
                throw new Error(`Response is not valid JSON. ${text ? text.slice(0, 160) : ''}`);
            }
        }

        /**
         * Vyrenderuje 1 riadok tabuľky prihlášok podľa dát zo servera.
         * Server posiela "rows": [{ id, osoba, kurz, stav, created_at, can_decide, url_show, url_approve, url_reject }]
         */
        function renderRow(r) {
            const canDecide = !!r.can_decide;

            // URL pre approve/reject doplníme o ajax=1 až tu (nie v controlleri ani v HTML)
            const approveUrl = new URL(r.url_approve, window.location.origin);
            approveUrl.searchParams.set('ajax', '1');

            const rejectUrl = new URL(r.url_reject, window.location.origin);
            rejectUrl.searchParams.set('ajax', '1');

            return `
            <tr data-id="${r.id}">
                <td>${r.id}</td>
                <td>${escapeHtml(r.osoba)}</td>
                <td>${escapeHtml(r.kurz)}</td>
                <td>
                    <span class="badge ${badgeClassFor(r.stav)} js-stav">
                        ${escapeHtml(r.stav)}
                    </span>
                </td>
                <td>${escapeHtml(r.created_at)}</td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="${r.url_show}">
                        Detail
                    </a>

                    ${canDecide ? `
                        <a class="btn btn-sm btn-outline-success ms-2 js-approve" href="${approveUrl.toString()}">
                            Schváliť
                        </a>
                        <a class="btn btn-sm btn-outline-danger ms-2 js-reject" href="${rejectUrl.toString()}">
                            Zamietnuť
                        </a>
                    ` : ``}
                </td>
            </tr>
        `;
        }

        /**
         * Načíta tabuľku podľa aktuálne zvolených filtrov v <form>.
         * Používa GET + ajax=1, server vráti JSON s rows.
         */
        async function loadFiltered() {
            const url = new URL(window.location.href);

            // FormData zoberie hodnoty z <select name="stav"> a <select name="id_kurz"> + hidden inputs c/a
            const fd = new FormData(form);

            // Reset query string, potom nastavíme podľa formu
            url.search = '';
            for (const [k, v] of fd.entries()) {
                url.searchParams.set(k, v);
            }

            // Povieme controlleru, že chceme JSON
            url.searchParams.set('ajax', '1');

            const data = await fetchJson(url.toString(), { method: 'GET' });
            if (!data || data.ok !== true || !Array.isArray(data.rows)) {
                throw new Error('Unexpected JSON structure from adminPrihlaska.index');
            }

            tbody.innerHTML = data.rows.map(renderRow).join('');
        }

        /**
         * Vykoná approve/reject akciu nad prihláškou.
         * - musí byť POST (controller to vyžaduje)
         * - očakáva JSON { ok: true, id, stav }
         */
        async function decide(url, confirmText) {
            if (busy) return; // ochrana pred double click

            const ok = window.confirm(confirmText);
            if (!ok) return;

            busy = true;
            try {
                const data = await fetchJson(url, { method: 'POST' });
                if (!data || data.ok !== true) {
                    throw new Error('AJAX approve/reject returned ok != true');
                }

                // Jednoduchá stratégia: po zmene stavu znovu načítame tabuľku podľa filtrov
                await loadFiltered();
            } finally {
                busy = false;
            }
        }

        // --- Events ---

        // Delegácia klikov v tabuľke: zachytí klik na approve/reject aj pre novovygenerované riadky
        tbody.addEventListener('click', function (e) {
            const approve = e.target.closest('a.js-approve');
            const reject = e.target.closest('a.js-reject');

            if (!approve && !reject) return;

            e.preventDefault();

            if (approve) {
                decide(approve.href, 'Schváliť prihlášku?')
                    .catch(err => {
                        console.error(err);
                        alert('Nastala chyba pri schvaľovaní.');
                    });
            } else if (reject) {
                decide(reject.href, 'Zamietnuť prihlášku?')
                    .catch(err => {
                        console.error(err);
                        alert('Nastala chyba pri zamietnutí.');
                    });
            }
        });

        // Keď sa zmení filter (select), načítame tabuľku cez AJAX.
        form.addEventListener('change', function () {
            if (busy) return;
            loadFiltered().catch(err => {
                // fallback: ak AJAX zlyhá, spravíme klasický submit (degraduje bez JS)
                console.error(err);
                form.submit();
            });
        });

        // Pri submit (klik na Filtrovať) zabránime reloadu a spravíme AJAX
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (busy) return;
            loadFiltered().catch(err => {
                console.error(err);
                form.submit();
            });
        });

        // Voliteľné: pri načítaní stránky môžeš tabuľku nechať tak (server-side render),
        // alebo načítať cez AJAX hneď.
        // loadFiltered().catch(() => {});
    })();


});
