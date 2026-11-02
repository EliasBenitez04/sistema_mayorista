<script>
document.addEventListener('DOMContentLoaded', function () {
    var dialog = document.getElementById('ec-detail-dialog');
    var body = document.getElementById('ec-dialog-body');
    var close = document.getElementById('ec-dialog-close');
    if (dialog && body) {
        document.addEventListener('click', function (event) {
            var trigger = event.target.closest('.js-ec-open');
            if (!trigger) return;
            var target = document.getElementById(trigger.getAttribute('data-template'));
            if (!target) return;
            body.replaceChildren(target.content.cloneNode(true));
            if (typeof dialog.showModal === 'function') {
                dialog.showModal();
            }
        });
        close.addEventListener('click', function () { dialog.close(); });
        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) dialog.close();
        });
    }

    var filterButton = document.getElementById('ec-mobile-filters');
    var filters = document.getElementById('ec-filters');
    if (filterButton && filters) {
        filterButton.addEventListener('click', function () {
            var expanded = filters.classList.toggle('ec-filters-open');
            filterButton.setAttribute('aria-expanded', String(expanded));
            if (expanded) filters.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    var form = document.getElementById('ec-filter-form');
    if (form) {
        form.querySelectorAll('[data-auto-submit]').forEach(function (select) {
            select.addEventListener('change', function () { form.submit(); });
        });
    }
    var fullScreenButton = document.getElementById('ec-screen-toggle');
    if (fullScreenButton) {
        fullScreenButton.addEventListener('click', function () {
            var active = document.body.classList.toggle('ec-fullscreen');
            fullScreenButton.setAttribute('aria-pressed', String(active));
            fullScreenButton.title = active ? 'Volver al panel con menú lateral' : 'Mostrar catálogo como página completa';
            fullScreenButton.innerHTML = active
                ? '<i class="fas fa-compress-arrows-alt"></i><span>Volver al panel</span>'
                : '<i class="fas fa-expand-arrows-alt"></i><span>Vista tienda</span>';
        });
    }
});
</script>