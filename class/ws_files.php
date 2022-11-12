<?php

class ws_files
{

    public $result = array();
    public $errorFile;
    public $docContent = array();

    public function ws_scandir($dir, $files_type_admitted){
        $files = array_diff(scandir($dir), array('..', '.'));
        foreach ($files as $_file){
            $filetype = $this->ws_get_mimetype($_file);
            if(in_array($filetype, $files_type_admitted)){
                //$this->result[] = $_file;
                $this->result[] = array('name'=>$_file, 'type'=>$filetype);
            }
            //$this->result[] = array('name'=>$_file, 'type'=>$this->get_mimetype($_file));
        }

        return $this->result;
    }

    function ws_get_mimetype($filepath) {
        if(!preg_match('/\.[^\/\\\\]+$/',$filepath)) {
            return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filepath);
        }
        switch(strtolower(preg_replace('/^.*\./','',$filepath))) {
            // START MS Office 2007 Docs
            case 'docx':
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            case 'docm':
                return 'application/vnd.ms-word.document.macroEnabled.12';
            case 'dotx':
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
            case 'dotm':
                return 'application/vnd.ms-word.template.macroEnabled.12';
            case 'xlsx':
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            case 'xlsm':
                return 'application/vnd.ms-excel.sheet.macroEnabled.12';
            case 'xltx':
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
            case 'xltm':
                return 'application/vnd.ms-excel.template.macroEnabled.12';
            case 'xlsb':
                return 'application/vnd.ms-excel.sheet.binary.macroEnabled.12';
            case 'xlam':
                return 'application/vnd.ms-excel.addin.macroEnabled.12';
            case 'pptx':
                return 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
            case 'pptm':
                return 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
            case 'ppsx':
                return 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
            case 'ppsm':
                return 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12';
            case 'potx':
                return 'application/vnd.openxmlformats-officedocument.presentationml.template';
            case 'potm':
                return 'application/vnd.ms-powerpoint.template.macroEnabled.12';
            case 'ppam':
                return 'application/vnd.ms-powerpoint.addin.macroEnabled.12';
            case 'sldx':
                return 'application/vnd.openxmlformats-officedocument.presentationml.slide';
            case 'sldm':
                return 'application/vnd.ms-powerpoint.slide.macroEnabled.12';
            case 'one':
                return 'application/msonenote';
            case 'onetoc2':
                return 'application/msonenote';
            case 'onetmp':
                return 'application/msonenote';
            case 'onepkg':
                return 'application/msonenote';
            case 'thmx':
                return 'application/vnd.ms-officetheme';
            case 'doc':
                return 'application/msword';
            default:
                return 'other';
            //END MS Office 2007 Docs

        }
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filepath);
    }

    /**
     * Parse file content
     * @param $content
     * @param $docSeparator
     * @param $docStructure
     * @return array|mixed|string
     */
    function ws_parse_file_content($content, $docSeparator, $docStructure){
        $contentExplode = explode($docSeparator, $content);
        $strExplode = explode($docSeparator, $docStructure);

        if(!is_array($strExplode)) return $this->errorFile = 'Invalid file structure, missing structure definition in plugin configuration.';
        if(!is_array($contentExplode)) return $this->errorFile = 'Invalid file structure, missing separator.';
        if(is_array($contentExplode) && (count($contentExplode) <= 1 )) return $this->errorFile = 'Invalid file structure, incorrect number of fields '.count($contentExplode).' expected '.count($strExplode).'.';
        if(is_array($contentExplode) && count($contentExplode) !== count($strExplode)) return $this->errorFile = 'Invalid file structure, number of fields does not match with structure definition in plugin configuration, found '.count($contentExplode).' expected '.count($strExplode).'.';

        foreach ($contentExplode as $ri=>$re){
            $this->docContent[$strExplode[$ri]] = trim($re);
        }
        return $this->docContent;

    }

    function ws_delete_file($filename){
        return unlink($filename);
    }


}