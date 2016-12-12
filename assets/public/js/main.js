/**
 * Created by james on 01/10/2016.
 */
(function($){

    var field_conditions_met = function(form, conditionArr){

        // loop through all conditional arrays, return true if found
        var found = false;

        $.each(conditionArr, function (index, condition) {

            var equals = condition['operator'] === '!=' ? false : true;

            switch(condition['field_type']){
                case 'checkbox':
                case 'radio':

                    var _elem = form.find('[name="'+condition['field']+'[]"][value="'+condition['value']+'"], [name="'+condition['field']+'"][value="'+condition['value']+'"]');
                    if(_elem !== undefined) {
                        if (( equals && _elem.prop('checked') == true ) || (!equals && _elem.prop('checked') == false)) {
                            found = true;
                            return false;
                        }
                    }

                    break;
                case 'select':

                    //todo: Multi select .val() brings back array
                    var _elem = form.find('select[name="'+condition['field']+'[]"], [name="'+condition['field']+'"]');
                    if(_elem !== undefined){
                        if (( equals && ""+_elem.val() == ""+condition['value'] ) || (!equals && ""+_elem.val() !== ""+condition['value'])) {
                            found = true;
                            return false;
                        }
                    }

                    break;
            }
        });

        return found;
    };

    $(document).ready(function(){

        var _forms = $('.wpdf-form');
        _forms.each(function(){
            var _form = $(this);

            // process display data
            var _wpdf_display = _form.data('wpdf-display');
            if(_wpdf_display !== undefined){

                _form.on('input change wpdf_display_check', 'input, select', function(){
                    $.each(_wpdf_display, function(key, conditionArr){

                        // find either multiple element "name[]", or single element "name"
                        var _elem = _form.find('[name="'+key+'[]"],[name="'+key+'"]');
                        if( field_conditions_met(_form, conditionArr) == true){
                            _elem.parents('.form-row').show();
                        }else{
                            _elem.parents('.form-row').hide();
                        }
                    });
                } );

                // trigger conditional check on all inputs
                _form.find('input, select').trigger('wpdf_display_check');
            }
        })

    });

})(jQuery);

(function($){

    $(document).ready(function(){

        var _number_fields = $('.wpdf-input-number');

        if(_number_fields.length == 0){
            return;
        }

        _number_fields.each(function(){

            var _slider = $(this).find('.wpdf-range-slider');
            if(_slider.length > 0){
                var _min = _slider.data('min');
                var _max = _slider.data('max');
                var _step = _slider.data('step');
                var _range = _slider.data('range');

                var _config = {
                    min: _min,
                    max: _max,
                    step: _step,
                    values: []
                };

                var inputs = [];

                if ( _range == 'yes' ) {
                    _config.range = true;
                    inputs.push($(this).find('input[name$="[min]"]'));
                    inputs.push($(this).find('input[name$="[max]"]'));

                    _config.values.push(inputs[0].val());
                    _config.values.push(inputs[1].val());

                }else{
                    inputs.push($(this).find('input'));

                    _config.values.push(inputs[0].val());
                }

                // set input values depending on slider type
                _config.slide = function( event, ui ) {
                    if ( _range == 'yes' ) {
                        // ui.values [low, high]
                        inputs[0].val(ui.values[0]);
                        inputs[1].val(ui.values[1]);
                    }else{
                        // ui.value
                        inputs[0].val(ui.value);
                    }
                };

                _slider.slider(_config).each(function() {

                    // Add labels to slider whose values
                    // are specified by min, max

                    // Get the options for this slider (specified above)
                    var opt = $(this).data().uiSlider.options;

                    // Get the number of possible values
                    var vals = opt.max - opt.min;

                    // Position the labels
                    for (var i = 0; i <= vals; i++) {

                        // Create a new element and position it with percentages
                        var el = $('<label>' + (i + opt.min) + '</label>').css('left', (i/vals*100) + '%');

                        // Add the element inside #slider
                        _slider.append(el);

                    }

                });
            }

        });

    });

})(jQuery);