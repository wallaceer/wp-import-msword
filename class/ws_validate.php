<?php

class ws_validate
{

    public $validateError;

    static $patterText = '([0-9a-zA-Z-_ #@^§ç])';

    static $patternEmail = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';

    /**
     * Checks is the provided email address is formally valid
     *
     * @param string $email
     *            email address to be checked
     * @return true if the email is valid, false otherwise
     */
    function valid_email($email)
    {
        if (preg_match(self::$patternEmail, $email))
            return true;
        else
            return $this->validateError = 'Email not valid';
    }

    function valid_text($text)
    {
        if (preg_match(self::$patterText, $text))
            return true;
        else
            return $this->validateError = 'Text not valid';
    }


    /**
     * Strip tags
     * @param unknown $text
     * @param string $tags
     * @param string $invert
     * @return mixed|unknown
     */
    function strip_tags_content($text, $tags = '', $invert = FALSE) {

        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if(is_array($tags) AND count($tags) > 0) {
            if($invert == FALSE) {
                return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            }
            else {
                return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
            }
        }
        elseif($invert == FALSE) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        return $text;
    }



}