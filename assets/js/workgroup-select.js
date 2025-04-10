import $ from 'jquery';
import 'select2';

// Quand le document est prêt
$(document).ready(function() {
    $('#work_group_members').select2({
        placeholder: 'Sélectionnez les membres du groupe',
        allowClear: true,
        width: '100%',
    });
});
