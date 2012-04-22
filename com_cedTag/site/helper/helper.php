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
    function getAllTagModel($count = 25, $sorting = 'sizeasort', $reverse = 1)
    {
        $dbo = JFactory::getDBO();

        $query = "select count(*) as ct,name as name,t.hits as hits from #__cedtag_term_content as tc inner join
                    #__cedtag_term as t on t.id=tc.tid  where t.published='1' group by(tid) order by ct DESC;";
        $dbo->setQuery($query);
        $rows = $dbo->loadObjectList();

        CedTagsHelper::addCss();

        // linearizes the Pareto distribution
        return $this->mappingFrequencyToSize($rows);
    }

    private function mappingFrequencyToSize($rows)
    {
        if (isset($rows) && !empty($rows)) {
            $mappingFrequencyToSizeAlgorithm = CedTagsHelper::param('mappingFrequencyToSizeAlgorithm', 'dynamicbuckets');

            if ($mappingFrequencyToSizeAlgorithm == 'pareto') {
                return $this->mappingFrequencyToSizeWithPareto($rows);
            } else if ($mappingFrequencyToSizeAlgorithm == 'dynamicbuckets') {
                return $this->mappingFrequencyToSizeWithDynamicBuckets($rows);
            } else {
                return $this->mappingFrequencyToSizeWithFixedBuckets($rows);
            }

        }
        return $rows;
    }

    private function mappingFrequencyToSizeWithDynamicBuckets($rows)
    {
        $tags = array();
        foreach ($rows as $row) {
            $tags[$row->name] = $row->ct;
            $taghit[$row->name] = $row->hits;
            $tagcount[$row->name] = $row->ct;
            $buckets[$row->ct] = "";
        }

        // order buckets by frequency
        ksort($buckets);
        $count = count($buckets) - 1;

        // min-max font sizes
        $max_size = 250; // max font size in %
        $min_size = 100; // min font size in %
        $range = $max_size - $min_size;
        // step is the difference in font size from one bucket to the next
        $step = $range / $count;

        // populate buckets with font sizes
        $i = $min_size;
        foreach ($buckets AS $key => $value) {
            $buckets[$key] = $i;
            $i += $step;
        }
        $result = array();
        while (list($tagname, $tagsize) = each($tags)) {
            $term = new stdClass();
            $term->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($tagname));
            $term->name = CedTagsHelper::ucwords($tagname);
            $term->size = $buckets[$tagsize];
            $term->ct = $tagcount[$tagname];
            $term->hits = $taghit[$tagname];
            $term->class = 'tag';
            $result[] = $term;
        }
        return $result;
    }


    private function mappingFrequencyToSizeWithPareto($rows)
    {
        $tags = array();
        foreach ($rows as $row) {
            $tags[$row->name] = $row->ct;
            $taghit[$row->name] = $row->hits;
            $tagcount[$row->name] = $row->ct;
        }
        $maxSize = $rows[0]->ct;
        $minSize = 1;

        $tags = $this->fromParetoCurve($tags, $minSize, $maxSize);

        $result = array();
        while (list($tagname, $tagsize) = each($tags)) {
            $term = new stdClass();
            $term->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($tagname));
            $term->name = CedTagsHelper::ucwords($tagname);
            $term->size = $tagsize;
            $term->ct = $tagcount[$tagname];
            $term->hits = $taghit[$tagname];
            $term->class = 'tag';
            $result[] = $term;
        }

        return $result;
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

        $query = "select count(*) as ct,name,hits from #__cedtag_term_content as tc inner join #__cedtag_term as t on t.id=tc.tid where t.published='1' group by(tid) order by ct desc";
        $dbo->setQuery($query, 0, $count);
        $rows = $dbo->loadObjectList();

        if (isset($rows) && !empty($rows)) {

            usort($rows, array('CedTagsHelper', 'tag_popularasort'));

            CedTagsHelper::addCss();

            $tag_sizes = 7;
            $total_tags = count($rows);
            $min_tags = $total_tags / $tag_sizes;
            $bucket_count = 1;
            $bucket_items = 0;
            $tags_set = 0;
            for ($index = 0; $index < $total_tags; $index++) {
                $row =& $rows[$index];
                //$row->link=JRoute::_('index.php?option=com_cedtag&task=tag&tag='.urlencode($row->name));
                $row->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($row->name));
                $last_count = 0;
                $tag_count = $row->ct;
                if (($bucket_items >= $min_tags) and $last_count != $tag_count and $bucket_count < $tag_sizes) {
                    $bucket_count++;
                    $bucket_items = 0;
                    // Calculate a new minimum number of tags for the remaining classes.
                    $remaining_tags = $total_tags - $tags_set;
                    $min_tags = $remaining_tags / $bucket_count;
                }
                $row->class = 'tag' . $bucket_count;
                $row->size = 65 + ($bucket_count * 10); //65 + ($tag_count * 10); //65 + ($tag_count * 10);
                $bucket_items++;
                $tags_set++;
                $last_count = $tag_count;
                $row->name = CedTagsHelper::ucwords($row->name);

            }
            usort($rows, array('CedTagsHelper', $sorting));

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


    /*
     * Pareto distribution
     * The Pareto distribution, named after the Italian economist Vilfredo Pareto, is a power law probability distribution that coincides
     * with social, scientific, geophysical, actuarial, and many other types of observable phenomena. Outside the field of economics it
     * is sometimes referred to as the Bradford distribution.
     *
     */
    function fromParetoCurve($weights, $minSize, $maxSize)
    {


        $logweights = array(); // array of log value of counts
        $output = array(); // output array of linearized count values

        // Convert each weight to its log value.
        foreach ($weights AS $tagname => $w)
        {
            // take each weight from input, convert to log, put into new array called logweights
            $logweights[$tagname] = log($w);
        }

        // MAX AND MIN OF logweights ARRAY
        $max = max(array_values($logweights));
        $min = min(array_values($logweights));

        foreach ($logweights AS $lw)
        {
            if ($lw < $min) {
                $min = $lw;
            }
            if ($lw > $max) {
                $max = $lw;
            }
        }

        // Now calculate the slope of a straight line, from min to max.
        if ($max > $min) {
            $slope = ($maxSize - $minSize) / ($max - $min);
        }

        $middle = ($minSize + $maxSize) / 2;

        foreach ($logweights AS $tagname => $w)
        {
            if ($max <= $min) { //With max=min all tags have the same weight.
                $output[$tagname] = $middle;
            } else { // Calculate the distance from the minimum for this weight.
                $distance = $w - $min; //Calculate the position on the slope for this distance.
                $result = $slope * $distance + $minSize; // If the tag turned out too small, set minSize.
                if ($result < $minSize) {
                    $result = $minSize;
                }
                //If the tag turned out too big, set maxSize.
                if ($result > $maxSize) {
                    $result = $maxSize;
                }
                $output[$tagname] = $result;
            }
        }
        return $output;
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