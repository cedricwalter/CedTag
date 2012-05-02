<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once JPATH_COMPONENT_SITE . '/helper/helper.php';
require_once JPATH_SITE . '/components/com_cedtag/wordcloud/tagcloud.php';


class CedTagModelAllTags extends JModel
{

    //        $yellowRed = array('FFA700', 'FFDF00', 'FF4F00', 'FFEE73');

    protected function populateState()
    {
        // Get the application object.
        $app = JFactory::getApplication();
        $params = $app->getParams('com_cedtag');

        // Load the parameters.
        $this->setState('params', $params);
    }

    function getWordle()
    {
        $app = JFactory::getApplication();
        $params = $app->getParams();

        $CedTagsHelper = new CedTagsHelper();


        $rows = $CedTagsHelper->getPopularTagModel();
        //$rows = $this->getModel("wordle");

        $full_text = array();
        foreach ($rows as $row) {
            $line = array('word' => $row->name, 'count' => $row->size, 'title' => $row->name, 'link' => $row->link);
            $full_text[] = $line;
        }

        $font = JPATH_SITE . './components/com_cedtag/wordcloud/Arial.ttf';
        $width = intval($params->get('wordleWidth', 300));
        $height = intval($params->get('wordleHeight', 300));

        $cloud = new WordCloud(915, 692, $font, $full_text);

        $palette = $params->get('wordlePalette', 'CC6600,FFFBD0,FF9900,C13100');

        $palette = Palette::get_palette_from_hex($cloud->get_image(),
            explode(",", $palette)
        );
        $cloud->render($palette);

        // Render the cloud in a temporary file, and return its base64-encoded content
        $file = tempnam(JPATH_SITE . "/cache/com_cedtag/", 'img');
        imagepng($cloud->get_image(), $file);
        $img64 = base64_encode(file_get_contents($file));
        unlink($file);
        imagedestroy($cloud->get_image());

        $result = array();
        $result['cloud'] = $cloud;
        $result['img64'] = $img64;

        return $result;
    }

    function getAllTags()
    {
        $rows = $this->getModel("allTags");
        return $rows;
    }

    private function getModel($paramPrefixName)
    {
        $app = JFactory::getApplication();
        $params = $app->getParams();

        $document = JFactory::getDocument();

        $description = CedTagsHelper::param($paramPrefixName . 'MetaDescription');
        $document->setDescription(CedTagsHelper::truncate($description));

        $keywords = CedTagsHelper::param($paramPrefixName . 'MetaKeywords');
        $document->setMetadata($paramPrefixName . 'Keywords', $keywords);

        $title = CedTagsHelper::param($paramPrefixName . 'Title');
        $document->setTitle($title);

        $CedTagsHelper = new CedTagsHelper();

        $count = intval($params->get($paramPrefixName . 'Count', 25));
        //$sorting = $params->get('wordleTagOrder', 'sizeasort');
        $reverse = intval($params->get($paramPrefixName . 'Reverse', 1));

        $rows = $CedTagsHelper->getAllTagModel();
        return $rows;
    }


}
