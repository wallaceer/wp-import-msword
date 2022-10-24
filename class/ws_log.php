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


    /**
     * @param $message
     */
    public function logWrite($message){
        if(is_array($message)) {
            $message = json_encode($message);
        }
        $file = fopen(self::$logfile,"a");
        fwrite($file, "\n" . date('Y-m-d h:i:s') . " :: " . $message);
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


}