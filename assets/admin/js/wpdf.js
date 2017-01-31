/**
 * Global Functions
 */

(function($) {

    // reindex rows
    $.fn.wpdf_reindex_rows = function () {
        $(this).each(function (rowIndex) {
            /// find each input with a name attribute inside each row
            $(this).find('input[name], select[name], textarea[name]').each(function () {
                var name;
                name = $(this).attr('name');
                name = name.replace(/field\[[0-9]*\]/g, 'field[' + rowIndex + ']');
                $(this).attr('name', name);
            });

            // for test purposes to visually see order num
            var _order_input = $(this).find('input[name$="[order]"]');
            if(_order_input.length > 0){
                _order_input.val(rowIndex);
            }
        });
    };

})(jQuery);

/**
 * Drag & Drop field selector
 */
(function($){

    $(document).ready(function(){

        var debug = false;
        var _sortable_elem = $('#sortable');
        var _draggable_elem = $( ".draggable" );
        var _placeholder_text  = $('#sortable .placeholder').text();
        var templates = {
            text: $('#field-placeholder .wpdf-panel[data-field-type="text"]').clone().show(),
            textarea: $('#field-placeholder .wpdf-panel[data-field-type="textarea"]').clone().show(),
            select: $('#field-placeholder .wpdf-panel[data-field-type="select"]').clone().show(),
            checkbox: $('#field-placeholder .wpdf-panel[data-field-type="checkbox"]').clone().show(),
            radio: $('#field-placeholder .wpdf-panel[data-field-type="radio"]').clone().show(),
            file: $('#field-placeholder .wpdf-panel[data-field-type="file"]').clone().show(),
            number: $('#field-placeholder .wpdf-panel[data-field-type="number"]').clone().show()
        };

        _draggable_elem.click(function(){

            // load template for file
            var template = templates[$(this).data('field')].clone();
            template.addClass('wpdf-panel--active wpdf-panel--new');
            _sortable_elem.append(template);
            _sortable_elem.find('.wpdf-panel--new').wrap('<li class="draggable ui-state-highlight ui-draggable ui-draggable-handle wpdf-dropped-item"></li>').removeClass('wpdf-panel--new');
            $('body').trigger('wpdf_element_added', template);

            _sortable_elem.find('.placeholder').hide();

            // re-indexed rows
            _sortable_elem.find('li').wpdf_reindex_rows();

            // trigger that element has been changed
            $(document).trigger('wpdf_changed');

        });

        _sortable_elem.sortable({
            placeholder: "sortable-placeholder",
            handle: '.wpdf-panel__header',
            // toleranceElement: '> a',
            items: 'li:not(.placeholder)',
            over: function() {
                _sortable_elem.find('.placeholder').hide();
            },
            out: function() {

                if( _sortable_elem.find('li:not(.placeholder)').length <= 2){
                    _sortable_elem.find('.placeholder').show();
                }
            },
            stop: function() {
                _sortable_elem.find('.placeholder').hide();
            }
        });

        /**
         * make field buttons draggable to sortable fields
         */
        _draggable_elem.draggable({
            connectToSortable: "#sortable",
            helper: "clone",
            revert: "invalid",
            stop: function() {
                // _sortable_elem.find('.placeholder').remove();
            },
            start: function(event, ui){
                $(ui.helper).width($(this).width());
                $(ui.helper).height($(this).height());
            }
        });

        /**
         * When field is dropped, add class and change contents
         */
        _sortable_elem.on( "sortreceive", function( event, ui ) {

            if(debug){
                console.log('sortreceive');
            }

            // load template for file
            var template = templates[ui.item.data('field')].clone();
            if(template.length > 0){
                $(ui.helper).addClass('wpdf-dropped-item');
                $(ui.helper).html(template);
                var panel = $(ui.helper).find('.wpdf-panel').addClass('wpdf-panel--active');
                $('body').trigger('wpdf_element_added', panel);
            }

            // trigger that element has been changed
            $(document).trigger('wpdf_changed');
        } );

        /**
         * Remove fixed height when sorting has been stopped
         */
        _sortable_elem.on('sortstop', function(event, ui){

            if(debug){
                console.log('sortstop');
            }

            $(ui.item).height('auto');
            $(ui.item).width('auto');

            // reindex rows
            if(debug){
                console.log('reindex field rows');
            }
            _sortable_elem.find('li').wpdf_reindex_rows();

            // trigger that element has been changed
            $(document).trigger('wpdf_changed');
        });

        /**
         * Set width and height when sorting has been started,
         * change placeholder to the items height
         */
        _sortable_elem.on('sortstart', function(event, ui){

            if(debug){
                console.log('sortstart');
            }

            if(!$(ui.item).hasClass('wpdf-dropped-item')){
                console.log('found class!');
                return;
            }

            // set width and height of element
            var _element_height = $(ui.item).height();
            $(ui.item).height(_element_height);
            $(ui.item).width($('#sortable').width());

            // set height of placeholder
            $('#sortable .sortable-placeholder').height(_element_height).text(_placeholder_text);
        });

        // set height of placeholder
        _sortable_elem.find('.placeholder').height($('.wpdf-cols').height() - 40);

        if( _sortable_elem.find('li.ui-state-default').length > 0){
            _sortable_elem.find('.placeholder').hide();
        }

        // reindex rows
        if(debug){
            console.log('reindex field rows');
        }
        _sortable_elem.find('li').wpdf_reindex_rows();
    });

    $(document).on('click', '.wpdf-del-field', function(e){
        e.preventDefault();
        var result = confirm('Are you sure you want to delete this field?');
        if(result) {
            $(this).parents('li').remove();
            if ($('#sortable li:not(.placeholder)').length == 0) {
                var _placeholder = $('#sortable .placeholder');
                _placeholder.height($('.wpdf-cols').height() - 40);
                _placeholder.show();
            }

            // trigger that element has been changed
            $(document).trigger('wpdf_changed');
        }
    });

})(jQuery);

/**
 * File Boxed
 */
(function($){

    $(document).ready(function() {

        $(document).on('click', '.wpdf-panel__header .wpdf-panel__toggle', function () {
            $(this).parents('.wpdf-panel').toggleClass('wpdf-panel--active');
        });

    });

})(jQuery);

/**
 * Panel Name
 */
(function($){

    $(document).ready(function() {

        $(document).on('keyup change', '.wpdf-input--label', function(){
            $(this).parents('.wpdf-panel').find('.wpdf-panel__header span.name').text( $(this).val() );
        });

    });

})(jQuery);

/**
 * Repeater Block
 */
(function($){

    var repeater_templates = {};
    var repeater_indexs = {};
    var repeater_prefix = {};

    $(document).ready(function(){

        $('.wpdf-repeater').each(function(){

            var _repeater = $(this);
            var _template_name = _repeater.data('templateName');
            if(repeater_templates[_template_name] == undefined){
                repeater_templates[_template_name] = $( _repeater.find('script.wpdf-repeater-template').html() ).removeClass('wpdf-repeater__template');
            }

            var _template_index = _repeater.data('templateIndex');
            if(repeater_indexs[_template_name] == undefined){
                if(_template_index){
                    repeater_indexs[_template_name] = new RegExp(_template_index);
                }else{
                    repeater_indexs[_template_name] = /\[[0-9]*\]$/g;
                }
            }

            var _template_index = _repeater.data('templatePrefix');
            if(repeater_prefix[_template_name] == undefined){
                if(_template_index){
                    repeater_prefix[_template_name] = _repeater.data('templatePrefix');
                }else{
                    repeater_prefix[_template_name] = '';
                }
            }

            // reindex last []
            _repeater.find('.wpdf-repeater-row').each(function(elIndex){
                $(this).find('input[name], select[name]').each(function () {
                    var name;
                    name = $(this).attr('name');
                    name = name.replace(repeater_indexs[_template_name], repeater_prefix[_template_name] + '[' + elIndex + ']');
                    $(this).attr('name', name);
                });
            });

            // trigger that element has been changed
            _repeater.trigger('wpdf_repeater_init',_repeater);
        });
    });

    $(document).on('click', '.wpdf-add-row', function(e){
        e.preventDefault();

        var _repeater = $(this).parents('.wpdf-repeater');
        var _template_name = _repeater.data('templateName');

        if(repeater_templates[_template_name] == undefined){
            console.error("Template could not be found, has not been stored in templates array!");
            return;
        }

        var _repeater_container = _repeater.find('.wpdf-repeater-container');
        var _elem = repeater_templates[_template_name].clone();

        if(_repeater_container.length > 0){
            _repeater_container.append(_elem);
        }else{
            _repeater.append(_elem);
        }

        // reindex last []
        _repeater.find('.wpdf-repeater-row').each(function(elIndex){
            $(this).find('input[name], select[name], textarea[name]').each(function () {
                var name;
                name = $(this).attr('name');
                name = name.replace(repeater_indexs[_template_name], repeater_prefix[_template_name] + '[' + elIndex + ']');
                $(this).attr('name', name);
            });
        });

        _repeater.trigger('wpdf_repeater_added');
        $('body').trigger('wpdf_element_added', _elem);

        // reindex field it belongs to
        $('#sortable li').wpdf_reindex_rows();

        // trigger that element has been changed
        $(document).trigger('wpdf_changed');
    });

    $(document).on('click', '.wpdf-del-row', function(e){
        e.preventDefault();

        var _repeater = $(this).parents('.wpdf-repeater');
        var _min = 0;
        if(_repeater.data('min')) {
            _min = parseInt(_repeater.data('min'));
        }

        if(_repeater.find('.wpdf-repeater-row').length > _min) {
            var _row = $(this).parents('.wpdf-repeater-row');
            _row.remove();
            _repeater.trigger('wpdf_repeater_removed');
        }

        // trigger that element has been changed
        $(document).trigger('wpdf_changed');
    });

})(jQuery);

/**
 * Field Validation Rules
 */
(function($){

    var _disabled = {};
    var _validation_templates = {};

    /**
     * Re-Index rules
     */
    var reIndexRules = function(){
        $('.wpdf-validation-repeater').each(function () {
            reIndexRule($(this));
        });
    };

    var reIndexRule = function(_elem){
        _elem.find('.wpdf-validation-row').each(function(rowIndex){
            $(this).find('input[name], select[name], textarea[name]').each(function(){
                var name;
                name = $(this).attr('name');
                name = name.replace(/field\[(\d*)\]\[validation\]\[(\d*)\]\[(\w*)\]/g, 'field[$1][validation]['+rowIndex+'][$3]');
                $(this).attr('name', name);
            });
        });
    };

    /**
     * Disable / Enable validation rules on type dropdowns
     * @param _repeater
     * @param _validation_selector
     */
    var onRuleTypeChange = function(_repeater, _validation_selector){
        var _validation_type = _validation_selector.val();

        _disabled = {};

        _repeater.find('.wpdf-validation-row').each(function(i){
            var _select = $(this).find('.validation_type');
            var _current_val = _select.val();

            // skip empty values or current selectbox
            if(_current_val !== ''){
                if(_disabled[_current_val] !== undefined){
                    // _select.val('');
                    _disabled[_current_val]++;
                }else{
                    _disabled[_current_val] = 1;
                }
            }
        });

        // clear currently selected validation type due to pre existing type
        if(_validation_type !== "" && _disabled[_validation_type] > 1){
            _validation_selector.val('');
        }

        // enable all options
        $('.wpdf-validation-repeater .validation_type option').prop('disabled', false);

        if( !$.isEmptyObject(_disabled) ){

            // disable currently selected validation types
            $('.wpdf-validation-repeater .validation_type').each(function(){

                var _val = $(this).val();
                for(var validation_rule in _disabled){
                    if(validation_rule != _val){
                        $(this).find('option[value="'+validation_rule+'"]').prop('disabled', true);
                    }
                }
            });

        }

        // todo: load validation type fields
        var _ruleParent = _validation_selector.parents('.wpdf-validation-row');
        var _val = _validation_selector.val();
        var _active_rule = _ruleParent.data('rule');

        if(_val != '' && _val != _active_rule){

            _ruleParent.data('rule', _val);
            if(_validation_templates[_val] !== undefined){
                _ruleParent.find('.wpdf-validation__rule').html(_validation_templates[_val]);

                $('body').trigger('wpdf_element_added', _ruleParent.find('.wpdf-validation__rule'));

                // reindex field it belongs to
                $('#sortable li').wpdf_reindex_rows();
            }
        }

        if(_val === ''){
            _ruleParent.data('rule', '');
            _ruleParent.find('.wpdf-validation__rule').html('');
        }

        reIndexRules();
    }

    $(document).ready(function(){

        $('.wpdf-validation__rule').each(function(){
            var _key = $(this).data('rule');
            _validation_templates[_key] = $(this).html();
        });
    });

    $(document).on('wpdf_repeater_init', '.wpdf-validation-repeater', function(){

        var _repeater = $(this);
        if(_repeater.find('.wpdf-validation-row').length > 0){
            reIndexRule($(this));
        }
    });

    $(document).on('wpdf_repeater_added', '.wpdf-validation-repeater', function(){

        var _repeater = $(this);
        var _validation_selector = $(this);

        onRuleTypeChange(_repeater, _validation_selector);

        // trigger that element has been changed
        $(document).trigger('wpdf_changed');
    });

    $(document).on('wpdf_repeater_removed', '.wpdf-validation-repeater', function(){

        var _repeater = $(this);
        var _validation_selector = $(this);

        onRuleTypeChange(_repeater, _validation_selector);

        // trigger that element has been changed
        $(document).trigger('wpdf_changed');
    });

    $(document).on('change', '.wpdf-validation-repeater .validation_type', function(){

        var _repeater = $(this).parents('.wpdf-validation-repeater');
        var _validation_selector = $(this);
        onRuleTypeChange(_repeater, _validation_selector);
    });

})(jQuery);

/**
 * Help Tooltips
 */
(function($) {

    $(document).ready(function(){

        $('.wpdf-tooltip, .wpdf-tooltip-blank').each(function(){

            if($(this).attr('title').length > 0) {
                $(this).tipTip({
                    defaultPosition: "right"
                });
            }else{
                $(this).hide();
            }
        });

        $('body').on('wpdf_element_added', function(event, elem){
           $(elem).find('.wpdf-tooltip, .wpdf-tooltip-blank').each(function(){

               if($(this).attr('title').length > 0) {
                   $(this).tipTip({
                       defaultPosition: "right"
                   });
               }else{
                   $(this).hide();
               }
           });
        });

    });

})(jQuery);

/**
 * Field Value Repeater
 */
(function($){

    var _setClass = 'wpdf-data__key--set';

    var _sanitizeLabel = function(data){
        return data.toLowerCase().replace(/ /g,"_");
    };

    $(document).on('change', '.wpdf-field__values .wpdf-data__label', function(){

        var _row = $(this).parents('.wpdf-repeater-row');
        var _keyField = _row.find('.wpdf-data__key');
        var _defaultField = _row.find('.wpdf-data__default');

        if(!_keyField.hasClass(_setClass)){

            var _val = _sanitizeLabel( $(this).val());
            _keyField.val(_val);

            if(_defaultField.length > 0) {
                _defaultField.val(_val);
            }
        }

    });

    $(document).on('change', '.wpdf-field__values .wpdf-data__key', function(){
        if(!$(this).hasClass(_setClass)){
            $(this).addClass(_setClass);
        }
    });

})(jQuery);

/**
 * Iris Colour Picker
 */
(function($){

    $(document).ready(function(){

        $('.wpdf-color-picker-input').iris({
            change: function( event, ui ) {
                $( this ).parent().find( '.wpdf-color-pick-preview' ).css({ backgroundColor: ui.color.toString() });
            }
        });
        $(document).click(function (e) {
            if ( $('.wpdf-color-picker-input').length > 0 && $(".colour-picker, .iris-picker, .iris-picker-inner").is(':visible') && !$(e.target).is(".colour-picker, .iris-picker, .iris-picker-inner")) {
                $('.wpdf-color-picker-input').iris('hide');
                return false;
            }
        });
        $('.wpdf-color-picker-input, .wpdf-color-pick-preview').click(function (event) {
            $('.wpdf-color-picker-input').iris('hide');
            $(this).parent().find('.wpdf-color-picker-input').iris('show');
            return false;
        });

    });

})(jQuery);

/**
 * Alert when leaving changes
 */
(function($){

    var _changed = false;

    $(document).on('wpdf_changed', function(){
        if ( false === _changed ){
            _changed = true;

            var wrap = $('#error-wrapper');
            if ( wrap.length > 0 ) {
                wrap.html('<p class="notice notice-warning wpdf-notice">You have unsaved changes, to save these click the update button.</p>');
            }
        }
    });

    $(document).on('click', '.wpdf-form-manager--inputs input, .wpdf-form-manager--inputs select, .wpdf-form-manager--inputs textarea', function(){

        if( $(this).hasClass('wpdf-header__submit') ){
            return;
        }

        $(document).trigger('wpdf_changed');
    });

    $(document).on('keyup change', '.wpdf-form-manager--inputs input, .wpdf-form-manager--inputs select, .wpdf-form-manager--inputs textarea', function(){
        $(document).trigger('wpdf_changed');
    });

    /**
     * If submit is clicked, skip onBeforeunload
     */
    $(document).on('click', '.wpdf-header__submit', function(){
        _changed = false;
    });

    /**
     * Show browser warning
     *
     * @returns {string}
     */
    window.onbeforeunload = function() {

        if ( true === _changed ){
            return "";
        }
    }

    /**
     * Required field validation
     */
    $(document).ready(function(){
       var _form = $('#wpdf-form-fields');
       var _error = false;
       if ( _form.length > 0 ){

           $(this).on('submit', function( e ){

               // deactivate all required fields
               $(this).find('.wpdf-col--required').removeClass('wpdf-col--required');

               $(this).find('.wpdf-fields .wpdf-input__required').each(function(){

                   if( $(this).val().trim() == "" ){
                       e.preventDefault();
                       _error = true;
                       $(this).parents('.wpdf-panel').addClass('wpdf-panel--active');
                       $(this).parents('.wpdf-col').addClass('wpdf-col--required');
                   }
               });

               if(_error) {
                   var wrap = $('#error-wrapper');
                   if (wrap.length > 0) {
                       wrap.html('<p class="notice notice-error wpdf-notice">Please make sure you have filled in all required fields before saving, highlighted in red below.</p>');
                   }
               }
           });
       }
    });

})(jQuery);