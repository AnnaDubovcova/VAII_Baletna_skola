//alert('JS pre obdobie-form je načítaný');


document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('obdobie-form');
    if (!form) {
        return;
    }

    // DEBUG – ak chceš overiť, že sa skript načítal, môžeš odkomentovať:
    // alert('JS pre obdobie-form je načítaný');

    var nazov = document.getElementById('nazov');
    var datumOd = document.getElementById('datum_od');
    var datumDo = document.getElementById('datum_do');

    form.addEventListener('submit', function (event) {
        var valid = true;

        clearError(nazov);
        clearError(datumOd);
        clearError(datumDo);

        // NÁZOV
        if (!nazov.value.trim()) {
            showError(nazov, 'Názov je povinný.');
            valid = false;
        } else if (nazov.value.length > 100) {
            showError(nazov, 'Názov môže mať max. 100 znakov.');
            valid = false;
        }

        // DÁTUM OD
        if (!datumOd.value) {
            showError(datumOd, 'Dátum od je povinný.');
            valid = false;
        }

        // DÁTUM DO
        if (!datumDo.value) {
            showError(datumDo, 'Dátum do je povinný.');
            valid = false;
        }

        // PORADIE DÁTUMOV
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

        // odstránime len JS hlášky, serverové (bez js-client) necháme
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

        // vytvoríme vlastný invalid-feedback
        var feedback = document.createElement('div');
        feedback.className = 'invalid-feedback js-client';
        feedback.textContent = message;

        // vložíme ho za input (aby fungovalo Bootstrap pravidlo .form-control.is-invalid ~ .invalid-feedback)
        container.appendChild(feedback);
    }
});
