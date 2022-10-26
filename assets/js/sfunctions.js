jQuery(document).ready(function($) {
    var $ = jQuery.noConflict();
    $("#button_create").click(function(e) {
        e.preventDefault();
        var accessToken = $("#access_token").val();
        if(accessToken != ''){
            $.ajax({
                type: "POST",
                url: "../wp-content/plugins/wp-import-word/cron/ws_job.php",
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
