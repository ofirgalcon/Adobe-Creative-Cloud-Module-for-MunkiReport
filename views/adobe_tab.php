<div id="adobe-tab"></div>
<div id="lister" style="font-size: large; float: right;">
    <a href="/show/listing/adobe/adobe" title="List">
        <i class="btn btn-default tab-btn fa fa-list"></i>
    </a>
</div>
<div id="report_btn" style="font-size: large; float: right;">
    <a href="/show/report/adobe/adobe" title="Report">
        <i class="btn btn-default tab-btn fa fa-th"></i>
    </a>
</div>
<h2><i class="fa fa-creative-commons"></i> <span data-i18n="adobe.listing.title"></span> <span id="adobe-cnt" class="badge"></span></h2>

<div id="adobe-msg" data-i18n="loading"></div>
<div id="adobe-table-view" class="hide">
    <table class="table table-striped table-condensed table-bordered" id="adobe-tab-table">
        <thead>
            <tr>
                <th data-i18n="adobe.app_name_short"></th>
                <th data-i18n="adobe.sapcode"></th>
                <th data-i18n="adobe.base_version"></th>
                <th data-i18n="adobe.year_edition"></th>
                <th data-i18n="adobe.installed_version"></th>
                <th data-i18n="adobe.latest_version"></th>
                <th data-i18n="adobe.is_up_to_date"></th>
                <th data-i18n="adobe.description"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
$(document).on('appReady', function(){
    $.getJSON(appUrl + '/module/adobe/get_tab_data/' + serialNumber, function(data){
        
        // Handle both data.msg format and direct array format
        var adobeData = data.msg || data;
        
        // Check if we have data
        if(!adobeData || !adobeData.length){
            $('#adobe-msg').text(i18n.t('no_data'));
            $('#adobe-cnt').text(''); // Clear badge when no data
            return;
        }
        
        // Set the badge count
        $('#adobe-cnt').text(adobeData.length);
        
        // Hide loading message and show table
        $('#adobe-msg').addClass('hide');
        $('#adobe-table-view').removeClass('hide');
        
        var tbody = $('#adobe-tab-table tbody');
        tbody.empty();
        
        // Process each Adobe app record
        $.each(adobeData, function(index, app){
            var row = $('<tr>');
            row.append($('<td>').text(app.app_name || ''));
            row.append($('<td>').text(app.sapcode || ''));
            row.append($('<td>').text(app.base_version || ''));
            row.append($('<td>').text(app.year_edition || ''));
            row.append($('<td>').text(app.installed_version || ''));
            row.append($('<td>').text(app.latest_version || ''));
            
            // Format the up-to-date status with color coding
            var statusCell = $('<td>');
            if (app.is_up_to_date === '1' || app.is_up_to_date === 1) {
                statusCell.html('<span class="label label-success">' + i18n.t('yes') + '</span>');
            } else if (app.is_up_to_date === '0' || app.is_up_to_date === 0) {
                statusCell.html('<span class="label label-danger">' + i18n.t('no') + '</span>');
            } else {
                statusCell.html('<span class="label label-warning">' + i18n.t('unknown') + '</span>');
            }
            row.append(statusCell);
            
            row.append($('<td>').text(app.description || ''));
            tbody.append(row);
        });
    })
    .fail(function(){
        $('#adobe-msg').text(i18n.t('error.loading'));
    });
});
</script>
