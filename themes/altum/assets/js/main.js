/* Submit disable after 1 click */
$('[type=submit][name=submit]').on('click', (event) => {
    $(event.currentTarget).addClass('disabled');

    let text = $(event.currentTarget).text();
    let loader = '<div class="spinner-grow spinner-grow-sm"><span class="sr-only">Loading...</span></div>';
    $(event.currentTarget).html(loader);

    setTimeout(() => {
        $(event.currentTarget).removeClass('disabled');
        $(event.currentTarget).text(text);
    }, 3000);

});

/* Confirm delete handler */
$('body').on('click', '[data-confirm]', (event) => {
    let message = $(event.currentTarget).attr('data-confirm');

    if(!confirm(message)) return false;
});

/* Custom links */
$('[data-href]').on('click', event => {
    let url = $(event.currentTarget).data('href');

    fade_out_redirect({ url, full: true });
});

/* Enable tooltips everywhere */
$('[data-toggle="tooltip"]').tooltip();

/* Some global variables */
window.altum = {};
let global_token = document.querySelector('input[name="global_token"]').value;
let url = document.querySelector('input[name="url"]').value;
let decimal_point = document.querySelector('[name="number_decimal_point"]').value;
let thousands_separator = document.querySelector('[name="number_thousands_separator"]').value;
