jQuery(document).ready(function($) {
    var $ = jQuery.noConflict();
    $("#button_create").click(function(e) {
        e.preventDefault();
        var accessToken = $("#access_token").val();
        if(accessToken != ''){
            $.ajax({
                type: "POST",
                url: "../wp-content/plugins/wp-import-word/actions/ws_job.php",
                data: {
                    access_token: accessToken
                },
                success: function(result) {
                    $("#result_create_posts").html(result);
                },
                error: function(result) {
                    $("#result_create_posts").html(result);
                }
            });
        }
    });

});

/**
 * Show/Hide section relative to parse document configurations
 */
function parse_document_config() {
    var $ = jQuery.noConflict();
    var checkbox = $('[name="wp_import_word_document_parsing"]');

        if (checkbox.is(':checked')){
            document.getElementById('parse_document_config').style.display = 'inline';
        } else {
            document.getElementById('parse_document_config').style.display = 'none';
        }

}
