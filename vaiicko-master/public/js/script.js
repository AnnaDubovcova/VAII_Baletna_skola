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

    (function () {
        const form = document.getElementById('admin-prihlaska-filter');
        const tbody = document.getElementById('admin-prihlasky-body');

        if (!form || !tbody) return;

        function badgeClassFor(stav) {
            switch (stav) {
                case 'nova': return 'bg-secondary';
                case 'schvalena': return 'bg-success';
                case 'zamietnuta': return 'bg-danger';
                case 'zrusena': return 'bg-warning';
                default: return 'bg-secondary';
            }
        }

        function escapeHtml(str) {
            return String(str ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderRow(r) {
            const canDecide = !!r.can_decide;

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
                <a class="btn btn-sm btn-outline-secondary"
                   href="${r.url_show}">
                    Detail
                </a>

                ${canDecide ? `
                <a class="btn btn-sm btn-outline-success ms-2 js-approve"
                   href="${r.url_approve}&ajax=1">
                    Schváliť
                </a>
                <a class="btn btn-sm btn-outline-danger ms-2 js-reject"
                   href="${r.url_reject}&ajax=1">
                    Zamietnuť
                </a>
                ` : ``}
            </td>
        </tr>`;
        }

        async function loadFiltered() {
            const url = new URL(window.location.href);
            const fd = new FormData(form);

            url.search = '';
            for (const [k, v] of fd.entries()) {
                url.searchParams.set(k, v);
            }
            url.searchParams.set('ajax', '1');

            const res = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json' }
            });

            const data = await res.json();
            if (!data.ok) throw new Error('AJAX filter failed');

            tbody.innerHTML = data.rows.map(renderRow).join('');
        }

        // approve / reject
        tbody.addEventListener('click', async function (e) {
            const a = e.target.closest('a.js-approve, a.js-reject');
            if (!a) return;

            e.preventDefault();

            const isApprove = a.classList.contains('js-approve');
            const ok = window.confirm(isApprove
                ? 'Schváliť prihlášku?'
                : 'Zamietnuť prihlášku?');

            if (!ok) return;

            try {
                const res = await fetch(a.href, {
                    headers: { 'Accept': 'application/json' }
                });

                const data = await res.json();
                if (!data.ok) throw new Error('AJAX approve/reject failed');

                // vždy znovu načítame tabuľku podľa filtrov
                await loadFiltered();

            } catch (err) {
                console.error(err);
                alert('Nastala chyba pri spracovaní požiadavky.');
            }
        });

        // filter change
        form.addEventListener('change', function () {
            loadFiltered().catch(err => {
                console.error(err);
                form.submit();
            });
        });

        // filter submit
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            loadFiltered().catch(err => {
                console.error(err);
                form.submit();
            });
        });

    })();

});
