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
                        if (( equals && _elem.prop('checked') == true ) || (!equals && _elem.prop('checked', false))) {
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
