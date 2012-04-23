<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die('Restricted access');
class CedTagFrequencyMapping
{

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
        while (list($tagname, $tagsize) = each($tags)) {
            $term = new stdClass();
            $term->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($tagname));
            $term->name = CedTagsHelper::ucwords($tagname);
            $term->size = $buckets[$tagsize];
            $term->frequency = $tagsNameToRow[$tagname]->frequency;
            $term->hits = $tagsNameToRow[$tagname]->hits;
            $term->created =  $tagsNameToRow[$tagname]->created;
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
            $term->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($tagname));
            $term->name = CedTagsHelper::ucwords($tagname);
            $term->size = $tagsize;
            $term->frequency = $tagsNameToRow[$tagname]->frequency;
            $term->hits = $tagsNameToRow[$tagname]->hits;
            $term->created =  $tagsNameToRow[$tagname]->created;
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
        $logweights = array(); // array of log value of counts
        $output = array(); // output array of linearized count values

        // Convert each weight to its log value.
        foreach ($tags AS $tagname => $w)
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

    function mappingFrequencyToSizeWithFixedBuckets($tags)
    {

    }


    function mappingFrequencyToSizeWithMinMax($tags)
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
