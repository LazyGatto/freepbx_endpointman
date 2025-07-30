"use strict";

function epm_devices_document_ready() {
    // Brand -> Model
    $('#brand_edit').on('change', function() {
        var brandId = $(this).val();
        // Replace with your own endpoint or use preloaded data
        $.getJSON('your_backend_endpoint/models', {brand_id: brandId}, function(models) {
            var $model = $('#model_new');
            $model.empty();
            $.each(models, function(i, model) {
                $model.append($('<option>', { value: model.id, text: model.name }));
            });
            $model.trigger('change');
        });
    });

    // Model -> Template & Line
    $('#model_new').on('change', function() {
        var modelId = $(this).val();
        // Template
        $.getJSON('your_backend_endpoint/templates', {model_id: modelId}, function(templates) {
            var $template = $('#template_list');
            $template.empty();
            $.each(templates, function(i, template) {
                $template.append($('<option>', { value: template.id, text: template.name }));
            });
        });
        // Line
        $.getJSON('your_backend_endpoint/lines', {model_id: modelId}, function(lines) {
            var $line = $('#line_list');
            $line.empty();
            $.each(lines, function(i, line) {
                $line.append($('<option>', { value: line.id, text: line.name }));
            });
        });
    });

    // Bulk select/deselect
    $('#selecter').on('click', function() { $('#devList .device').prop('checked', true); });
    $('#deselecter').on('click', function() { $('#devList .device').prop('checked', false); });

    // Expand/Collapse all
    $('#expander').on('click', function() { $('.toggle_all').show(); $('#expander').hide(); $('#collapser').show(); });
    $('#collapser').on('click', function() { $('.toggle_all').hide(); $('#expander').show(); $('#collapser').hide(); });

    // Add device
    window.add_device = function() {
        $('#adding').append('<input type="hidden" name="sub_type" value="add"/>');
        $('#adding')[0].submit();
    };

    // Edit device
    window.edit_device = function(type, id, sub) {
        $('#adding').append('<input type="hidden" name="edit_id" value="'+ id +'"/><input type="hidden" name="sub_type" value="'+ type +'"/><input type="hidden" name="sub_type_sub" value="'+ sub +'"/>');
        $('#adding')[0].submit();
    };

    // Delete device
    window.delete_device = function(id) {
        if (confirm('Are you sure you want to delete this device?')) {
            submit_wtype('delete_device',id);
        }
    };

    // Add searched devices
    window.add_searched_devices = function() {
        $('#unmanaged').append('<input type="hidden" name="sub_type" value="add_selected_phones"/>');
        $('#unmanaged')[0].submit();
    };

    // Managed options (bulk)
    window.managed_options = function(type) {
        $('#managed').append('<input type="hidden" name="sub_type" value="'+ type +'"/>');
        $('#managed')[0].submit();
    };

    // Global options
    window.submit_global = function(type) {
        $('#globalmanaged').append('<input type="hidden" name="sub_type" value="'+ type +'"/>');
        $('#globalmanaged')[0].submit();
    };
    window.submit_global2 = function(type) {
        $('#globalmanaged2').append('<input type="hidden" name="sub_type" value="'+ type +'"/>');
        $('#globalmanaged2')[0].submit();
    };
    window.submit_global3 = function(type) {
        $('#globalmanaged3').append('<input type="hidden" name="sub_type" value="'+ type +'"/>');
        $('#globalmanaged3')[0].submit();
    };

    // Edit line or template
    window.submit_wtype = function(type, id) {
        $('#adding').append('<input type="hidden" name="edit_id" value="'+ id +'"/><input type="hidden" name="sub_type" value="'+ type +'"/>');
        $('#adding')[0].submit();
    };
    window.submit_stype = function(type, id) {
        window.open('config.php?display=epm_config&quietmode=1&handler=file&file=popup.html.php&module=endpointman&pop_type=edit_specifics&edit_id=' + id + '&rand=' + new Date().getTime(),'name2','height=700,width=750,scrollbars=yes,location=no');
        return false;
    };

    // Popup for template editing
    window.popitup = function(url, name, id) {
        if(id != '0') {
            var modelVal = $('#model_new').val() || '';
            var templateVal = $('#template_list').val() || '';
            var newwindow = window.open(url + '&model_list=' + modelVal + '&template_list=' + templateVal + '&rand=' + new Date().getTime(),'name2','height=1000,width=950,scrollbars=yes,location=no');
            if (window.focus) {newwindow.focus()}
            return false;
        }
    };
}

$(document).ready(epm_devices_document_ready);