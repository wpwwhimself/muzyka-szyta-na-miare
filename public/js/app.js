/**
 * Podświetlanie inputów
 */
const highlightInput = (input) => {
    document.querySelectorAll(`.input-container`).forEach(el => el.classList.add("ghost"))
    input.closest(".input-container").classList.remove("ghost");
    input.nextElementSibling.classList.add("accent", "bigger");
}
const clearHighlightInput = (input) => {
    document.querySelectorAll(`.input-container`).forEach(el => el.classList.remove("ghost"))
    input.nextElementSibling.classList.remove("accent", "bigger");
}

/**
 * File player
 */
const changeFilePlayerButton = (filename, icon) => {
    document.querySelectorAll(`.file-player[data-file-name="${filename}"] .fa-solid`)
        .forEach(icon => icon.classList.add("hidden"))
    document.querySelector(`.file-player[data-file-name="${filename}"] .fa-solid.fa-${icon}`)
        .classList.remove("hidden")
}
const disableFilePlayer = (filename) => {
    changeFilePlayerButton(filename, "circle-notch")
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
