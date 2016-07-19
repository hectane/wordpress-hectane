<?php

/*
Plugin Name: Hectane
Plugin URI: https://github.com/hectane/hectane
Description: Deliver all WordPress emails via Hectane.
Version: 0.1.6
Author: Nathan Osman
Author URI: https://quickmediasolutions.com
License: MIT
License URI: https://opensource.org/licenses/MIT
*/

require_once plugin_dir_path(__FILE__) . 'hectane_api.php';
require_once plugin_dir_path(__FILE__) . 'hectane_settings.php';

// Create the settings instance only if the admin is open
if(is_admin()) {
    new HectaneSettings();
}

// Only override the wp_mail() function if it doesn't exist
if (!function_exists('wp_mail')) {

    /**
     * Determine if a message contains HTML.
     */
    function hectane_isMessageHtml($message) {
        $tStart = strpos($message, '<');
        $tEnd = strpos($message, '>');
        if($tStart === 0 && $tEnd > 1) {
            $HMTL_ELEMENTS = array('!DOCTYPE'=>1, 'A'=>1, 'ABBR'=>1, 'ACRONYM'=>1, 'ADDRESS'=>1, 'APPLET'=>1, 'AREA'=>1, 'ARTICLE'=>1, 'ASIDE'=>1, 'AUDIO'=>1, 'B'=>1, 'BASE'=>1, 'BASEFONT'=>1, 'BDI'=>1, 'BDO'=>1, 'BIG'=>1, 'BLOCKQUOTE'=>1, 'BODY'=>1, 'BR'=>1, 'BUTTON'=>1, 'CANVAS'=>1, 'CAPTION'=>1, 'CENTER'=>1, 'CITE'=>1, 'CODE'=>1, 'COL'=>1, 'COLGROUP'=>1, 'DATALIST'=>1, 'DD'=>1, 'DEL'=>1, 'DETAILS'=>1, 'DFN'=>1, 'DIALOG'=>1, 'DIR'=>1, 'DIV'=>1, 'DL'=>1, 'DT'=>1, 'EM'=>1, 'EMBED'=>1, 'FIELDSET'=>1, 'FIGCAPTION'=>1, 'FIGURE'=>1, 'FONT'=>1, 'FOOTER'=>1, 'FORM'=>1, 'FRAME'=>1, 'FRAMESET'=>1, 'H1'=>1, 'HEAD'=>1, 'HEADER'=>1, 'HR'=>1, 'HTML'=>1, 'I'=>1, 'IFRAME'=>1, 'IMG'=>1, 'INPUT'=>1, 'INS'=>1, 'KBD'=>1, 'KEYGEN'=>1, 'LABEL'=>1, 'LEGEND'=>1, 'LI'=>1, 'LINK'=>1, 'MAIN'=>1, 'MAP'=>1, 'MARK'=>1, 'MENU'=>1, 'MENUITEM'=>1, 'META'=>1, 'METER'=>1, 'NAV'=>1, 'NOFRAMES'=>1, 'NOSCRIPT'=>1, 'OBJECT'=>1, 'OL'=>1, 'OPTGROUP'=>1, 'OPTION'=>1, 'OUTPUT'=>1, 'P'=>1, 'PARAM'=>1, 'PRE'=>1, 'PROGRESS'=>1, 'Q'=>1, 'RP'=>1, 'RT'=>1, 'RUBY'=>1, 'S'=>1, 'SAMP'=>1, 'SCRIPT'=>1, 'SECTION'=>1, 'SELECT'=>1, 'SMALL'=>1, 'SOURCE'=>1, 'SPAN'=>1, 'STRIKE'=>1, 'STRONG'=>1, 'STYLE'=>1, 'SUB'=>1, 'SUMMARY'=>1, 'SUP'=>1, 'TABLE'=>1, 'TBODY'=>1, 'TD'=>1, 'TEXTAREA'=>1, 'TFOOT'=>1, 'TH'=>1, 'THEAD'=>1, 'TIME'=>1, 'TITLE'=>1, 'TR'=>1, 'TRACK'=>1, 'TT'=>1, 'U'=>1, 'UL'=>1, 'VAR'=>1, 'VIDEO'=>1, 'WBR'=>1 );
            $fullTag = substr($message, $tStart + 1, $tEnd - $tStart - 1);
            $tag = strtoupper(explode(' ', $fullTag, 2)[0]);
            return isset($HMTL_ELEMENTS[$tag]);
        }
        return false;
    }

    /**
     * Turn headers into an associative array.
     *
     * This function works both when headers are passed as an array or a string
     * (compliant with wp_mail's $headers parameter).
     */
    function hectane_parseHeaders($headers) {
        if (is_string($headers))
            $headers = preg_split("/\\r\\n|\\r|\\n/", $headers);
        $ret = array();
        foreach ($headers as $header) {
            $keyvalue = explode(': ', $header);
            if (count($keyvalue) == 2)
                $ret[$keyvalue[0]] = $keyvalue[1];
        }
        return $ret;
    }

    /**
     * Return the content of email's 'From' field.
     */
    function hectane_emailFrom($headers) {
        if (isset($headers['From'])) {
            return $headers['From'];
        }
        else {
            return sprintf('WordPress <wordpress@%s>', $_SERVER['SERVER_NAME']);
        }
    }

    /**
     * Override the default implementation of wp_mail().
     *
     * This function is responsible for marshalling the parameters into the JSON
     * data that is sent to Hectane.
     */
    function wp_mail($to, $subject, $message, $headers='', $attachments=array()) {
        if(!is_array($to)) {
            $to = array($to);
        }
        $headers = hectane_parseHeaders($headers);
        $from = hectane_emailFrom($headers);
        unset($headers['From']);
        $email = array(
            'from' => $from,
            'to' => is_array($to) ? $to : array($to),
            'subject' => $subject,
            'headers' => $headers ? $headers : new stdClass()
        );
        if (hectane_isMessageHtml($message)) {
            $email['html'] = $message;
        }
        else {
            $email['text'] = $message;
        }
        return HectaneAPI::instance()->send($email);
    }
}

?>
