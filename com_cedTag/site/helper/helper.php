<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die('Restricted access');

require dirname(__FILE__) . '/FrequencyMapping.php';


class CedTagsHelper
{

    /**
     *
     * @param int $count
     * @param string $sorting
     * @param int $reverse
     * @return array|mixed|void
     */
    function getAllTagModel($count = 25, $sorting = 'sizeasort', $reverse = 1)
    {
        $this->cache = JFactory::getCache('com_cedtag', '');
        $rows = $this->cache->get("getAllTagModel");

        //if not found in cache
        if ($rows === false) {
            $dbo = JFactory::getDBO();

            $query = "select count(*) as frequency,name as name,t.hits as hits, t.created as created from #__cedtag_term_content as tc inner join
                    #__cedtag_term as t on t.id=tc.tid  where t.published='1' group by(tid) order by frequency DESC;";
            $dbo->setQuery($query);
            $rows = $dbo->loadObjectList();

            CedTagsHelper::addCss();
            $rows = $this->mappingFrequencyToSize($rows);

            $this->cache->store($rows, "getAllTagModel");
        }

        return $rows;
    }

    public function mappingFrequencyToSize($rows)
    {
        if (isset($rows) && !empty($rows)) {
            $mappingFrequencyToSizeAlgorithm = CedTagsHelper::param('mappingFrequencyToSizeAlgorithm', 'pareto');

            $CedTagFrequencyMapping = new CedTagFrequencyMapping();

            if ($mappingFrequencyToSizeAlgorithm == 'pareto') {
                return $CedTagFrequencyMapping->mappingFrequencyToSizeWithPareto($rows);
            } else if ($mappingFrequencyToSizeAlgorithm == 'dynamicbuckets') {
                return $CedTagFrequencyMapping->mappingFrequencyToSizeWithDynamicBuckets($rows);
            } else {
                return $CedTagFrequencyMapping->mappingFrequencyToSizeWithFixedBuckets($rows);
            }

        }
        return $rows;
    }


    function _buildOrderBy($order)
    {
        switch ($order)
        {
            case 'random':
                $orderBy = 'RAND()';
                break;
            case 'date' :
                $orderBy = 't.created';
                break;

            case 'rdate' :
                $orderBy = 't.created DESC';
                break;

            case 'alpha' :
                $orderBy = 't.name';
                break;

            case 'ralpha' :
                $orderBy = 't.name DESC';
                break;

            case 'hits' :
                $orderBy = 't.hits DESC';
                break;

            case 'rhits' :
                $orderBy = 't.hits';
                break;

            default :
                $orderBy = 'RAND()';
                break;

        }
        return $orderBy;
    }


    function getPopularTagModel($count = 25, $sorting = 'sizeasort', $reverse = 1)
    {
        $dbo = JFactory::getDBO();

        $query = "select count(*) as frequency,name,hits, t.created as created from #__cedtag_term_content as tc inner join #__cedtag_term as t on t.id=tc.tid where t.published='1' group by(tid) order by frequency desc";
        $dbo->setQuery($query, 0, $count);
        $rows = $dbo->loadObjectList();

        if (isset($rows) && !empty($rows)) {
            CedTagsHelper::addCss();
            $rows = $this->mappingFrequencyToSize($rows);

            usort($rows, array('CedTagsHelper', 'tag_popularasort'));

            if ($reverse) {
                $rows = array_reverse($rows);
            }
        }

        return $rows;
    }


    static function addTagcanvasJavascript($uuid1, $uuid2)
    {
        static $added;

        $document =& JFactory::getDocument();
        $document->addScript(JURI::base() . 'media/com_cedtag/js/tagcanvas.js?1.15');

        if (!isset($added)) {
            $added = true;
            $document->addScriptDeclaration("
                            var oopts = {
                                interval: " . CedTagsHelper::param('interval', 20) . ",
                                maxSpeed: " . CedTagsHelper::param('maxSpeed', 0.05) . ",
                                minSpeed: " . CedTagsHelper::param('minSpeed', 0.0) . ",
                                decel: " . CedTagsHelper::param('decel', 0.95) . ",
                                minBrightness: " . CedTagsHelper::param('minBrightness', 0.1) . ",
                                textColour: '" . CedTagsHelper::param('textColour', '#ff99ff') . "',
                                textHeight: " . CedTagsHelper::param('textHeight', 15) . ",
                                textFont: '" . CedTagsHelper::param('textFont', 'Helvetica, Arial, sans-serif') . "',
                                outlineColour: '" . CedTagsHelper::param('outlineColour', '#ffff99') . "',
                                outlineMethod: '" . CedTagsHelper::param('outlineMethod', 'outline') . "',
                                outlineThickness: " . CedTagsHelper::param('outlineThickness', 2) . ",
                                outlineOffset: " . CedTagsHelper::param('outlineOffset', 5) . ",
                                pulsateTo: " . CedTagsHelper::param('pulsateTo', 1.0) . ",
                                pulsateTime: " . CedTagsHelper::param('pulsateTime', 3) . ",
                                depth: " . CedTagsHelper::param('depth', 0.5) . ",
                                initial: " . CedTagsHelper::param('initial', null) . ",
                                freezeActive: " . CedTagsHelper::param('freezeActive', false) . ",
                                activeCursor: '" . CedTagsHelper::param('activeCursor', 'pointer') . "',
                                frontSelect: " . CedTagsHelper::param('frontSelect', false) . ",
                                txtOpt: " . CedTagsHelper::param('txtOpt', true) . ",
                                txtScale: " . CedTagsHelper::param('txtScale', 2) . ",
                                reverse: " . CedTagsHelper::param('reverse', false) . ",
                                hideTags: " . CedTagsHelper::param('hideTags', true) . ",
                                zoom: " . CedTagsHelper::param('zoom', 1.0) . ",
                                wheelZoom: " . CedTagsHelper::param('wheelZoom', true) . ",
                                zoomStep: " . CedTagsHelper::param('zoomStep', 0.05) . ",
                                zoomMax: " . CedTagsHelper::param('zoomMax', 3.0) . ",
                                zoomMin: " . CedTagsHelper::param('zoomMin', 0.3) . ",
                                shadow: '" . CedTagsHelper::param('shadow', '#000000') . "',
                                shadowBlur: " . CedTagsHelper::param('shadowBlur', 0) . ",
                                shadowOffset: " . CedTagsHelper::param('shadowOffset', '[0,0]') . ",
                                weight: " . CedTagsHelper::param('weight', false) . ",
                                weightMode: '" . CedTagsHelper::param('weightMode', 'size') . "',
                                weightSize: " . CedTagsHelper::param('weightSize', 1.0) . ",
                                weightGradient: " . CedTagsHelper::param('weightGradient', "{0:'#f00', 0.33:'#ff0', 0.66:'#0f0', 1:'#00f'}") . ",
                                weightFrom: " . CedTagsHelper::param('weightFrom', null) . ",
                                shape: '" . CedTagsHelper::param('shape', 'sphere') . "',
                                lock: " . CedTagsHelper::param('lock', null) . ",
                                tooltip: " . CedTagsHelper::param('tooltip', null) . ",
                                tooltipClass: '" . CedTagsHelper::param('tooltipClass', 'tctooltip') . "',
                                radiusX: " . CedTagsHelper::param('radiusX', 1) . ",
                                radiusY: " . CedTagsHelper::param('radiusY', 1) . ",
                                radiusZ: " . CedTagsHelper::param('radiusZ', 1) . ",
                                stretchX: " . CedTagsHelper::param('stretchX', 1) . ",
                                stretchY: " . CedTagsHelper::param('stretchY', 1) . ",
                                shuffleTags: " . CedTagsHelper::param('shuffleTags', false) . ",
                                noSelect: " . CedTagsHelper::param('noSelect', false) . ",
                                noMouse: " . CedTagsHelper::param('noMouse', false) . ",
                                imageScale: " . CedTagsHelper::param('imageScale', 1) . ",
                                freezeActive: " . CedTagsHelper::param('freezeActive', false) . "
                            };");


        }


        $document =& JFactory::getDocument();
        $document->addScriptDeclaration("
                window.onload = function() {
                try {
                  TagCanvas.Start('" . $uuid1 . "','" . $uuid2 . "', oopts);


                } catch(e) {

                  // something went wrong, hide the canvas container
                  document.getElementById('myCanvasContainer').style.display = 'none';
                }
              };");
        //TagCanvas.Start('cedLatestTags1','cedLatestTags2', oopts);
        //TagCanvas.Start('cedMostPopularTags1','cedMostPopularTags2', oopts);
        //TagCanvas.Start('cedCustomTagsCloud1','cedCustomTagsCloud2', oopts);
        //TagCanvas.Start('cedMostReadTags1','cedMostReadTags2', oopts);
        //TagCanvas.Start('cedMostPopularTags1','cedMostPopularTags2', oopts);

    }


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

        $useGoogleFonts = CedTagsHelper::param('useGoogleFonts', '1');
        if ($useGoogleFonts) {
            $googleFonts = explode("|", CedTagsHelper::param('googleFonts', "font-family: 'Open Sans', sans-serif;|Open+Sans"));
            $document->addStyleSheet('http://fonts.googleapis.com/css?family='.$googleFonts[1]);
        }


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
        if ($tag1->frequency == $tag2->frequency) {
            return 0;
        }
        return ($tag1->frequency < $tag2->frequency) ? -1 : 1;
    }

    static function tag_latestasort($tag1, $tag2)
    {
        if ($tag1->created == $tag2->created) {
            return 0;
        }
        return ($tag1->created < $tag2->created) ? -1 : 1;
    }

    static function tag_random($tag1, $tag2)
    {
        return rand(-1, 1);
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

    static function getExcludedWordList()
    {
        static $excludedArray;
        if (!isset($excludedArray)) {
            $lang = JFactory::getLanguage()->getDefault();
            $file = JPATH_ADMINISTRATOR . '/components/com_cedtag/stopwords/stopwords_' . $lang . '.php';
            if (!is_file($file)) {
                JFile::copy(JPATH_ADMINISTRATOR . '/components/com_cedtag/stopwords/stopwords_en-GB-default.php', $file);
            }
            $FileContent = trim(JFile::read($file));
            $excludedArray = explode(",", $FileContent);
        }

        return $excludedArray;
    }


    static function isValidName($name)
    {
        $name = CedTagsHelper::preHandle($name);

        if (empty($name)) {
            return false;
        }
        if (in_array($name, CedTagsHelper::getExcludedWordList())) {
            return false;
        }

        return $name;

    }
}

?>