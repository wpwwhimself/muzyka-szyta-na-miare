function openExtendoBlock(btn, key) {
    document.querySelector(`[data-ebid='${key}'] .body`).classList.toggle("hidden");
    btn.parentElement.querySelectorAll(`.toggles`).forEach(b => b.classList.toggle("hidden"));
}
