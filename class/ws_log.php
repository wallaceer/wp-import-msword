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
    public static $logfile = '/var/log/wp_import_word.log';

    public function __construct(){
        if(!is_dir(self::$logfile)){
            mkdir(self::$logfile, 777);
        }

    }

    /**
     * @param $message
     */
    public function logWrite($type='INFO', $message){
        if(is_array($message)) {
            $message = json_encode($message);
        }
        $file = fopen(self::$logfile,"a");
        fwrite($file, "\n" . date('Y-m-d h:i:s') . " :: " . $type . " :: " . $message);
        fclose($file);
    }

    /**
     * Load log content
     * @return false|string
     */
    public function logRead(){
        $file = fopen(self::$logfile,"r");
        $this->filecontent = fread($file, filesize(self::$logfile));
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


}