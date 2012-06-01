<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


class wikipedia
{

    var $wikipediaServer = 'http://www.wikipedia.org';


    function __construct()
    {
        $this->wikipediaServer = CedTagsHelper::param('wikipediaServer', 'http://www.wikipedia.org');
    }

    function wikidefinition($s)
    {
        $url = $this->wikipediaServer ."/w/api.php?action=opensearch&search=" . urlencode($s) . "&format=xml&limit=1";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
        curl_setopt($ch, CURLOPT_REFERER, "");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; he; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8");
        $page = curl_exec($ch);
        $xml = simplexml_load_string($page);
        if ((string)$xml->Section->Item->Description) {
            return array((string)$xml->Section->Item->Text, (string)$xml->Section->Item->Description, (string)$xml->Section->Item->Url);
        }
        return "";
    }

}
