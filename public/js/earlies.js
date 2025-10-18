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
            document.querySelector("#price-summary").innerHTML = table;
            checkMonthlyPaymentLimit(data.price);
        });
}

/**
 * checks monthly payment limit
 */
function checkMonthlyPaymentLimit(price) {
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
            document.querySelector("#monthly-payment-limit").innerHTML = table;

            let delayed_payment;
            if(data.when_to_ask == 0){
                delayed_payment = undefined;
            }else{
                let today = new Date();
                delayed_payment = (new Date(today.getFullYear(), today.getMonth() + res.when_to_ask, 1));
                delayed_payment = `${delayed_payment.getFullYear()}-${(delayed_payment.getMonth() + 1).toString().padStart(2, 0)}-${delayed_payment.getDate().toString().padStart(2, 0)}`;
            }
            document.getElementById("delayed_payment").value = delayed_payment;
        });
}
