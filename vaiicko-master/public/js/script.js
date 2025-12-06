document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('obdobie-form');
    if (!form) {
        return;
    }

    form.addEventListener('submit', (event) => {
        let valid = true;

        const nazov = form.querySelector('#nazov');
        const datumOd = form.querySelector('#datum_od');
        const datumDo = form.querySelector('#datum_do');

        // reset bootstrap tried
        [nazov, datumOd, datumDo].forEach(input => {
            input.classList.remove('is-invalid');
        });

        // Názov
        if (!nazov.value.trim()) {
            nazov.classList.add('is-invalid');
            valid = false;
        } else if (nazov.value.length > 100) {
            nazov.classList.add('is-invalid');
            valid = false;
        }

        // Dátumy
        if (!datumOd.value) {
            datumOd.classList.add('is-invalid');
            valid = false;
        }

        if (!datumDo.value) {
            datumDo.classList.add('is-invalid');
            valid = false;
        }

        if (datumOd.value && datumDo.value) {
            const od = new Date(datumOd.value);
            const doD = new Date(datumDo.value);
            if (doD < od) {
                datumDo.classList.add('is-invalid');
                valid = false;
            }
        }

        if (!valid) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
});
