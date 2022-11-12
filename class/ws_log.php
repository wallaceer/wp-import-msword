<?php

/**
 * Class ws_log
 */
class ws_log{

    /**
     * @var
     */
    public $_file;
    public $filecontent;

    /**
     * @var string
     */
    public static string $logfile = 'var/log/wp_import_word.log';

    public function __construct(){
        if(!is_file(plugin_dir_path( __FILE__ ) . '/../'.self::$logfile)){
            $this->logOpen('w');
        }
    }

    public function logOpen($type){
        return fopen(plugin_dir_path( __FILE__ ) . '/../'.self::$logfile, $type);
    }

    /**
     * @param $message
     */
    public function logWrite($type='INFO', $message){
        if(is_array($message)) {
            $message = json_encode($message);
        }
        $file = $this->logOpen('a');
        fwrite($file, "\n" . date('Y-m-d h:i:s') . " :: " . $type . " :: " . $message);
        fclose($file);
    }

    /**
     * Load log content
     * @return false|string
     */
    public function logRead(){
        $file = $this->logOpen('r');
        $this->filecontent = fread($file, filesize(plugin_dir_path( __FILE__ ) . '/../'.self::$logfile));
        fclose($file);
        return $this->filecontent;
    }

    /**
     * Format html log output
     * @param $logContent
     * @return mixed|string
     */
    function formatLog($logContent){
        if(preg_match("/ERROR/", $logContent)) $logContent = "<span style='background-color:#8f1919;color:#ffffff'>".$logContent."</span>";
        if(preg_match("/WARNING/", $logContent)) $logContent = "<span style='background-color:#aa5500;color:#ffffff'>".$logContent."</span>";
        if(preg_match("/INFO/", $logContent)) $logContent = "<span style='color:#23A455'>".$logContent."</span>";
        return $logContent;
    }

    /**
     * Debug wp_mail()
     * @param $result
     * @return void
     */
    function debug_wpmail( $result = false ) {

        if ( $result )
            return;

        global $ts_mail_errors, $phpmailer;

        if ( ! isset($ts_mail_errors) )
            $ts_mail_errors = array();

        if ( isset($phpmailer) )
            $ts_mail_errors[] = $phpmailer->ErrorInfo;

        print_r('<pre>');
        print_r($ts_mail_errors);
        print_r('</pre>');
    }

}