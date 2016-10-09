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
            dropdown: $('#field-placeholder .wpdf-panel[data-field-type="dropdown"]').clone().show(),
            checkbox: $('#field-placeholder .wpdf-panel[data-field-type="checkbox"]').clone().show(),
            radio: $('#field-placeholder .wpdf-panel[data-field-type="radio"]').clone().show()
        };

        _sortable_elem.sortable({
            placeholder: "sortable-placeholder",
            // toleranceElement: '> a',
            items: 'li:not(.placeholder)',
            over: function() {
                $('.placeholder').hide();
            },
            out: function() {
                $('.placeholder').show();
            },
            stop: function() {
                $('.placeholder').remove();
            }
        });

        _sortable_elem.disableSelection();

        /**
         * make field buttons draggable to sortable fields
         */
        _draggable_elem.draggable({
            connectToSortable: "#sortable",
            helper: "clone",
            revert: "invalid",
            stop: function() {
                _sortable_elem.find('.placeholder').remove();
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
                $(ui.helper).find('.wpdf-panel').addClass('wpdf-panel--active');
            }
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

        if( _sortable_elem.find('li.ui-state-default').length > 0){
            _sortable_elem.find('.placeholder').hide();
        }
    });

})(jQuery);

/**
 * File Boxed
 */
(function($){

    $(document).on('click', '.wpdf-panel__header', function(){
        $(this).parents('.wpdf-panel').toggleClass('wpdf-panel--active');
    });

})(jQuery);