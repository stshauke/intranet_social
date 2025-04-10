import './styles/app.scss';
import { Tooltip } from 'bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;
import 'select2/dist/css/select2.min.css';
import 'select2';
import './js/group-pagination.js';


// Activer Bootstrap tooltips
document.addEventListener('DOMContentLoaded', () => {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new Tooltip(tooltipTriggerEl);
    });

    // Activer Select2
    $('select.select2').select2({
        width: '100%',
        placeholder: 'Sélectionnez des membres',
        allowClear: true
    });
});
