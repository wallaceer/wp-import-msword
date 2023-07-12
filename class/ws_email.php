<?php

class ws_email{


    public function ws_email_send($docEmail, $contentError){
        
            $siteFromName = get_bloginfo( 'name' );
            $siteFromEmail = get_bloginfo( 'admin_email' );
            $headers = [
                "Content-Type: text/html; charset=UTF-8",
                "From: $siteFromName <$siteFromEmail>",
                "Cc: $siteFromEmail"
            ];
            $sent_message = wp_mail($docEmail, __('WP Import from Word Log','wpimportword'), $contentError, $headers);
            if ( $sent_message ) {
                // The message was sent.
                return ['result_message'=>__('The test message was sent. Check your email inbox.','wpimportword'),'sent_message'=>$sent_message];
            } else {
                // The message was not sent.
                return ['result_message'=>__('The message was not sent!','wpimportword'),'sent_message'=>$sent_message];
            }
        
    }
    

}