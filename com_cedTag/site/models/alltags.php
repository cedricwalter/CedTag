<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once JPATH_COMPONENT_SITE . '/helper/helper.php';
require_once JPATH_SITE . '/components/com_cedtag/wordcloud/tagcloud.php';


class CedTagModelAllTags extends JModel
{

    function getWordle()
    {
        $CedTagsHelper = new CedTagsHelper();

        $app = JFactory::getApplication();
        $input = $app->input;
        $params = $app->getParams();

        /*
        $count = intval($params->get('count', 25));
        $sorting = $params->get('sorting', 'sizeasort');
        $reverse = intval($params->get('reverse', 1));
        */
        $rows = $CedTagsHelper->getPopularTagModel();

        $full_text = array();
        foreach ($rows as $row) {
            $line = array('word' => $row->name, 'count' => $row->size, 'title' => $row->name, 'link' => $row->link);
            $full_text[] = $line;
        }

        $yellowRed = array('FFA700', 'FFDF00', 'FF4F00', 'FFEE73');

        $font = JPATH_SITE . './components/com_cedtag/wordcloud/Arial.ttf';
        $width = 700;
        $height = 700;
        $cloud = new WordCloud($width, $height, $font, $full_text);
        $palette = Palette::get_palette_from_hex($cloud->get_image(), array('CC6600', 'FFFBD0', 'FF9900', 'C13100'));
        $cloud->render($palette);

        // Render the cloud in a temporary file, and return its base64-encoded content
        $file = tempnam(getcwd(), 'img');
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
        $document =& JFactory::getDocument();

        $description = CedTagsHelper::param('metaDescription');
        $document->setDescription(CedTagsHelper::truncate($description));

        $keywords = CedTagsHelper::param('metaKeywords');
        $document->setMetadata('keywords', $keywords);

        $title = CedTagsHelper::param('title');
        $document->setTitle($title);

        $CedTagsHelper = new CedTagsHelper();

        $count = null; //intval($params->get('count', 25));
        $sorting = null; //$params->get('sorting', 'sizeasort');
        $reverse = null; //intval($params->get('reverse', 1));

        return $CedTagsHelper->getPopularTagModel();
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
}
