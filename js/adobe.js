// Adobe Up-to-Date Status formatter
var formatAdobeUpToDate = function(col, row) {
    var cell = $('td:eq('+col+')', row),
        value = cell.text().trim();
    
    switch (value) {
        case '1':
            value = mr.label(i18n.t('yes'), 'success');
            break;
        case '0':
            value = mr.label(i18n.t('no'), 'danger');
            break;
        default:
            value = mr.label(i18n.t('adobe.unknown_status'), 'warning');
    }
    
    cell.html(value);
}

// Adobe Up-to-Date Status filter
var is_up_to_date_filter = function(colNumber, d) {
    // Look for 'up_to_date' keyword
    if (d.search.value.match(/^up_to_date$/)) {
        // Add column specific search
        d.columns[colNumber].search.value = '= 1';
        // Clear global search
        d.search.value = '';
    }

    // Look for 'update_available' keyword  
    if (d.search.value.match(/^update_available$/)) {
        // Add column specific search
        d.columns[colNumber].search.value = '= 0';
        // Clear global search
        d.search.value = '';
    }

    // Look for 'unknown_status' keyword
    if (d.search.value.match(/^unknown_status$/)) {
        // Search for values that are neither 1 nor 0 (null/unknown status)
        d.columns[colNumber].search.value = '^(?!0$|1$).*';
        // Clear global search
        d.search.value = '';
    }
} 