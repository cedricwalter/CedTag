<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die('Restricted access');
class CedTagsHelper
{
    static function param($name, $default = '')
    {
        static $params;
        if (!isset($params)) {
            $params = JComponentHelper::getParams('com_cedtag');
        }

        return $params->get($name, $default);
    }

    static function addCss()
    {
        $document =& JFactory::getDocument();
        $document->addStyleSheet(JURI::base() . 'media/com_cedtag/css/tagcloud.css');
    }


    static function tag_alphasort($tag1, $tag2)
    {
        if ($tag1->name == $tag2->name) {
            return 0;
        }
        return ($tag1->name < $tag2->name) ? -1 : 1;
    }

    static function tag_popularasort($tag1, $tag2)
    {
        if ($tag1->ct == $tag2->ct) {
            return 0;
        }
        return ($tag1->ct < $tag2->ct) ? -1 : 1;
    }

    static function tag_latestasort($tag1, $tag2)
    {
        if ($tag1->created == $tag2->created) {
            return 0;
        }
        return ($tag1->created < $tag2->created) ? -1 : 1;
    }

    static function hitsasort($tag1, $tag2)
    {
        if ($tag1->hits == $tag2->hits) {
            return 0;
        }
        return ($tag1->hits < $tag2->hits) ? -1 : 1;
    }

    static function sizeasort($tag1, $tag2)
    {
        if ($tag1->size == $tag2->size) {
            return 0;
        }
        return ($tag1->size < $tag2->size) ? -1 : 1;
    }


    static function getComponentVersion()
    {
        static $version;

        if (!isset($version)) {
            $xml = JFactory::getXMLParser('Simple');
            $xmlFile = JPATH_ADMINISTRATOR . '/components/com_cedtag/tag.xml';
            if (file_exists($xmlFile)) {
                if ($xml->loadFile($xmlFile)) {
                    $root =& $xml->document;
                    $element =& $root->getElementByPath('version');
                    $version = $element ? $element->data() : '';
                }
            }
        }
        return $version;
    }

    static function preHandle($tag)
    {
        $tag = CedTagsHelper::tripChars($tag);
        $tag = JString::trim($tag);
        $tag = CedTagsHelper::unUrlTagname($tag);
        $toLowerCase = CedTagsHelper::param('lowcase', 1);
        if ($toLowerCase) {
            $tag = JString::strtolower($tag);
        }
        return $tag;
    }

    static function ucwords($word)
    {
        if (CedTagsHelper::param('capitalize')) {
            return JString::ucwords($word);
        } else {
            return $word;
        }
    }

    static function urlTagname($tagname)
    {
        return preg_replace(
            array('/-/', '/\+/'),
            array('%3A', '-'),
            urlencode($tagname)
        );
        //return urlencode($tagname);
    }

    static function unUrlTagname($tagname)
    {
        return preg_replace(
            '/[:-]/',
            ' ',
            urldecode($tagname)
        );
        //return urldecode($tagname);
    }

    static function truncate($blurb)
    {
        $blurb = strip_tags($blurb);
        $blurb = str_replace('"', '\"', $blurb);
        $words = explode(' ', trim($blurb));

        if (count($words) > 15) {
            $blurb = implode(' ', array_splice($words, 0, 15)) . '...';
        }

        return $blurb;
    }

    static function tripChars($name)
    {
        $stripChars = CedTagsHelper::param('StripCharacters');

        $stripCharList = array();
        $stripCharList = explode('|', $stripChars);
        $mustTripChars = array('|', "'", "\"");

        $stripCharList = array_merge($stripCharList, $mustTripChars);
        $stripCharList = array_unique($stripCharList);
        $finalRemoveChaList = array();
        foreach ($stripCharList as $c) {
            if ($c != '-') {
                $finalRemoveChaList[] = $c;
            }

        }

        $name = str_replace($finalRemoveChaList, '', $name);
        $name = str_replace('-', ' ', $name);
        return $name;
    }

    static function  isValidName($name)
    {
        $valid = true;

        $name = CedTagsHelper::preHandle($name);
        if (empty($name)) $valid = false;

        if ($valid) {
            return $name;
        } else {
            return false;
        }
    }
}

?>