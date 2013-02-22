<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die('Restricted access');

class CedTagFrequencyMapping extends JObject
{

    public function __construct()
    {
        parent::__construct();
    }

    /*
    http: //www.bytemycode.com/snippets/snippet/415/
    $tags = array('weddings' => 32, 'birthdays' => 41, 'landscapes' => 62, 'ham' => 51, 'chicken' => 23, 'food' => 91, 'turkey' => 47, 'windows' => 82, 'apple' => 27);

    printTagCloud($tags);
    */

    /**
     * http://softwaretimes.com/files/tag%20clouds,%20the%20new%20human%20.html
     * @param $rows
     * @return array
     */
    public function mappingFrequencyToSizeWithDynamicBuckets($rows)
    {
        $tags = array();
        $tagsNameToRow = array();
        foreach ($rows as $row) {
            $tags[$row->name] = $row->frequency;
            $tagsNameToRow[$row->name] = $row;
            $buckets[$row->frequency] = "";
        }

        // order buckets by frequency
        ksort($buckets);
        $count = count($buckets) - 1;
        if ($count == 0) {
            $count = 1;
        }

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
        while (list($tagName, $tagSize) = each($tags)) {
            $term = new stdClass();
            $term->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($tagName));
            $term->name = CedTagsHelper::ucwords($tagName);
            $term->size = $buckets[$tagSize];
            $term->frequency = $tagsNameToRow[$tagName]->frequency;
            $term->hits = $tagsNameToRow[$tagName]->hits;
            $term->created = $tagsNameToRow[$tagName]->created;
            $term->title = JText::sprintf('COM_CEDTAG_ITEMS_TITLE',
                (string)$term->frequency,
                (string)$term->name,
                (string)$term->created,
                (string)$term->hits
            );
            $term->class = 'cedtag';
            $result[] = $term;
        }
        return $result;
    }

    /**
     * http://rhodopsin.blogspot.com/2008/05/php-tag-cloud.html
     * http://en.wikipedia.org/wiki/Pareto_distribution
     * @param $rows
     * @return array
     */
    public function mappingFrequencyToSizeWithPareto($rows)
    {
        $tags = array();
        $tagsNameToRow = array();
        foreach ($rows as $row) {
            $tags[$row->name] = $row->frequency;
            $tagsNameToRow[$row->name] = $row;
        }
        $maxSize = $rows[0]->frequency;
        $minSize = 1;

        $tags = $this->fromParetoCurve($tags, $minSize, $maxSize);

        $result = array();
        while (list($tagname, $tagsize) = each($tags)) {
            $term = new stdClass();
            $term->size = $tagsize;

            $term->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($tagname));
            $term->name = CedTagsHelper::ucwords($tagname);
            $term->frequency = $tagsNameToRow[$tagname]->frequency;
            $term->hits = $tagsNameToRow[$tagname]->hits;
            $term->created = $tagsNameToRow[$tagname]->created;
            $term->title = JText::sprintf('COM_CEDTAG_ITEMS_TITLE',
                (string)$term->frequency,
                (string)$term->name,
                (string)$term->created,
                (string)$term->hits
            );

            $term->class = 'cedtag';
            $result[] = $term;
        }

        return $result;
    }


    /*
    * Pareto distribution
    * The Pareto distribution, named after the Italian economist Vilfredo Pareto, is a power law probability distribution that coincides
    * with social, scientific, geophysical, actuarial, and many other types of observable phenomena. Outside the field of economics it
    * is sometimes referred to as the Bradford distribution.
    *
    */
    private function fromParetoCurve($tags, $minSize, $maxSize)
    {
        $logWeights = array(); // array of log value of counts
        $output = array(); // output array of linearized count values

        // Convert each weight to its log value.
        foreach ($tags AS $tagName => $w) {
            // take each weight from input, convert to log, put into new array called logweights
            $logWeights[$tagName] = log($w);
        }

        // MAX AND MIN OF logweights ARRAY
        $max = max(array_values($logWeights));
        $min = min(array_values($logWeights));

        foreach ($logWeights AS $lw) {
            if ($lw < $min) {
                $min = $lw;
            }
            if ($lw > $max) {
                $max = $lw;
            }
        }

        // Now calculate the slope of a straight line, from min to max.
        $slope = 0;
        if ($max > $min) {
            $slope = ($maxSize - $minSize) / ($max - $min);
        }

        $middle = ($minSize + $maxSize) / 2;

        foreach ($logWeights AS $tagName => $w) {
            if ($max <= $min) { //With max=min all tags have the same weight.
                $output[$tagName] = $middle;
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
                $output[$tagName] = $result;
            }
        }
        return $output;
    }

    public function mappingFrequencyToSizeWithFixedBuckets($tags)
    {
        //TODO create fix size bucket
        return $this->mappingFrequencyToSizeWithDynamicBuckets($tags);
    }


    public function mappingFrequencyToSizeWithMinMax($tags)
    {
        // $tags is the array
        arsort($tags);

        $max_size = 32; // max font size in pixels
        $min_size = 12; // min font size in pixels

        // largest and smallest array values
        $max_qty = max(array_values($tags));
        $min_qty = min(array_values($tags));

        // find the range of values
        $spread = $max_qty - $min_qty;
        if ($spread == 0) { // we don't want to divide by zero
            $spread = 1;
        }
        // set the font-size increment
        $step = ($max_size - $min_size) / ($spread);

        // loop through the tag array
        foreach ($tags as $key => $value) {
            // calculate font-size
            // find the $value in excess of $min_qty
            // multiply by the font-size increment ($size)
            // and add the $min_size set above
            $size = round($min_size + (($value - $min_qty) * $step));

            echo '<a href="#" style="font-size: ' . $size . 'px" title="' . $value . ' things tagged with ' . $key . '">' . $key . '</a> ';
        }
    }


}
