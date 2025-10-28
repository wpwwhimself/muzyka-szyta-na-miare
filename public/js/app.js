// #region user cannot ask for password reset
document.querySelector(`.button[href$="/auth/password/reset"]`)?.classList.add("hidden");
// #endregion