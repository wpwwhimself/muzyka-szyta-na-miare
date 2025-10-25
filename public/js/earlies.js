/**
 * Opens/closes sections
 */
function openExtendoBlock(btn, key) {
    document.querySelector(`[data-ebid='${key}'] .body`).classList.toggle("hidden");
    btn.parentElement.querySelectorAll(`.toggles`).forEach(b => b.classList.toggle("hidden"));
}

/**
 * runs price calculation
 */
function reQuestCalcPrice(labels, client_id) {
    document.querySelector("#price-summary .loader").classList.remove("hidden");

    fetch(`/api/price_calc`, {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            _token: '{{ csrf_token() }}',
            labels: labels,
            client_id: client_id,
            quoting: true
        }),
    })
        .then(res => res.json())
        .then(({data, table}) => {
            document.querySelector("#price-summary").replaceWith(fromHTML(table));
            checkMonthlyPaymentLimit(data.price);
        });
}

/**
 * checks monthly payment limit
 */
function checkMonthlyPaymentLimit(price) {
    document.querySelector("#delayed-payments-summary .loader").classList.remove("hidden");

    fetch(`/api/monthly_payment_limit`, {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            _token: '{{ csrf_token() }}',
            amount: price,
        }),
    })
        .then(res => res.json())
        .then(({data, table}) => {
            document.querySelector("#delayed-payments-summary").replaceWith(fromHTML(table));

            let delayed_payment;
            if(data.when_to_ask == 0){
                delayed_payment = undefined;
            }else{
                let today = new Date();
                delayed_payment = (new Date(today.getFullYear(), today.getMonth() + data.when_to_ask, 1));
                delayed_payment = `${delayed_payment.getFullYear()}-${(delayed_payment.getMonth() + 1).toString().padStart(2, 0)}-${delayed_payment.getDate().toString().padStart(2, 0)}`;
            }
            document.getElementById("delayed_payment").value = delayed_payment;
        });
}

/**
 * set up page reload
 */
function primeReload() {
    window.onfocus = function () { location.reload(true) }
}

/**
 * File player
 */
const changeFilePlayerButton = (filename, icon) => {
    document.querySelectorAll(`.file-player[data-file-name="${filename}"] [role="btn"]`)
        .forEach(cntnr => {
            console.log(cntnr.children[0].id, `mdi-${icon}`);
            cntnr.classList.toggle("hidden", cntnr.children[0].id != `mdi-${icon}`);
        });
}
const disableFilePlayer = (filename) => {
    changeFilePlayerButton(filename, "loading")
    showSeeker(filename, false)
}
const enableFilePlayer = (filename) => {
    changeFilePlayerButton(filename, "play")
    showSeeker(filename)
}
const startFilePlayer = (filename) => {
    document.querySelector(`.file-player[data-file-name="${filename}"] audio`).play()
    changeFilePlayerButton(filename, "pause")
}
const pauseFilePlayer = (filename) => {
    document.querySelector(`.file-player[data-file-name="${filename}"] audio`).pause()
    changeFilePlayerButton(filename, "play")
}

const durToTime = (duration) => {
    const minutes = Math.floor(duration / 60)
    const seconds = Math.floor(duration % 60)
    return `${minutes}:${seconds < 10 ? "0" + seconds : seconds}`
}

const showSeeker = (filename, show = true) => {
    const seeker = document.querySelector(`.file-player[data-file-name="${filename}"] .seeker`)
    if (show) {
        updateSeeker(filename)
        seeker.classList.remove("hidden")
    } else {
        seeker.classList.add("hidden")
    }
}

const updateSeeker = (filename) => {
    const seeker = document.querySelector(`.file-player[data-file-name="${filename}"] .seeker`)
    const audio = document.querySelector(`.file-player[data-file-name="${filename}"] audio`)

    seeker.innerHTML = `${durToTime(audio.currentTime)} / ${durToTime(audio.duration)}`
    seeker.style.setProperty("--progress", `${(audio.currentTime / audio.duration) * 100}%`)
}

const seekFilePlayer = (filename, event) => {
    const audio = document.querySelector(`.file-player[data-file-name="${filename}"] audio`)
    audio.currentTime = (event.offsetX / event.target.offsetWidth) * audio.duration
    updateSeeker(filename)
}
