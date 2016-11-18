(function() {
    tinymce.create("tinymce.plugins.wpdf_form_btn", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            //add new button
            ed.addButton("wpdf", {
                title : "Add Form",
                cmd : "wpdf_shortcode"
                // ,
                // image : "https://cdn3.iconfinder.com/data/icons/softwaredemo/PNG/32x32/Circle_Green.png"
            });

            ed.addCommand("wpdf_shortcode", function() {

                // basic add shortcode with all options
                // todo: add modal to display dropdown of available forms

                var return_text = '[wp_form form="" form_id="" /]';
                ed.execCommand("mceInsertContent", 0, return_text);
            });

        }
    });

    tinymce.PluginManager.add("wpdf_form_btn", tinymce.plugins.wpdf_form_btn);
})();