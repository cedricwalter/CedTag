<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';
require_once JPATH_SITE . '/components/com_cedtag/wordcloud/tagcloud.php';

class CedTagModelAllTags extends JModel
{

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

        $enableCache = intval($params->get('enableCache', 1));
        $cache = JFactory::getCache('com_cedtag', '');
        $cache->setCaching($enableCache);
        $cacheId = md5($params);
        $result = $cache->get($cacheId);

        //file not found in cache
        if ($result === false) {
            $CedTagsHelper = new CedTagsHelper();

            $count = intval($params->get('wordleCount', 25));
            $rows = $CedTagsHelper->getPopularTagModel($count);
            //$rows = $this->getModel("wordle");

            $tags = array();
            foreach ($rows as $row) {
                $line = array('word' => $row->name, 'count' => $row->size, 'title' => $row->name, 'link' => $row->link);
                $tags[] = $line;
            }

            $font = JPATH_ROOT . '/components/com_cedtag/wordcloud/Arial.ttf';
            error_log("Font at <".$font.">");

            $minFontSize = intval($params->get('wordleFontMin', 8));
            $maxFontSize = intval($params->get('wordleFontMax', 32));

            $width = intval($params->get('wordleWidth', 600));
            $height = intval($params->get('wordleHeight', 600));

            $placement = intval($params->get('wordlePlacement', FrequencyTable::WORDS_MAINLY_HORIZONTAL));

            $imageColor = $this->getImageColor($params);

            $cloud = new WordCloud($minFontSize, $maxFontSize, $width, $height, $font, $tags, $imageColor, $count, $placement);

            $palette = $this->getPalette($params, $cloud);
            $cloud->render($palette);

            $img64 = $this->renderCloud($cloud);

            // return model
            $result = array();
            $result['cloud'] = $cloud;
            $result['img64'] = $img64;

            if ($enableCache) {
                $cache->store($result, $cacheId);
            }
        }
        return $result;
    }

    private function getImageColor($params)
    {
        $transparency = intval($params->get('wordleTransparency', 127));
        $backgroundColor = $params->get('wordleBackgroundColor', '000000');
        $imageColor = ARRAY(
            HEXDEC(SUBSTR($backgroundColor, 0, 2)),
            HEXDEC(SUBSTR($backgroundColor, 2, 2)),
            HEXDEC(SUBSTR($backgroundColor, 4, 2)),
            $transparency,
        );
        return $imageColor;
    }

    private function renderCloud($cloud)
    { // Render the cloud in a temporary file, and return its base64-encoded content
        $file = JPATH_SITE . "/cache/com_cedtag/wordle" . uniqid() . ".png";
        imagepng($cloud->get_image(), $file);
        $img64 = base64_encode(file_get_contents($file));
        unlink($file);
        imagedestroy($cloud->get_image());
        return $img64;
    }

    /**
     * custom or preset palette
     *
     * @param $params
     * @param $cloud
     * @return array
     */
    private function getPalette($params, $cloud)
    {
        $useCustomPalette = $params->get('wordleUseCustomPalette', '0');
        if ($useCustomPalette) {
            $customPalette = $params->get('wordleCustomPalette', 'CC6600,FFFBD0,FF9900,C13100');
            $palette = Palette::get_palette_from_hex($cloud->get_image(),
                explode(",", $customPalette)
            );
            return $palette;
        } else {
            $palettes = array(
                'aqua' => array('BED661', '89E894', '78D5E3', '7AF5F5', '34DDDD', '93E2D5'),
                'yellow/blue' => array('FFCC00', 'CCCCCC', '666699'),
                'grey' => array('87907D', 'AAB6A2', '555555', '666666'),
                'brown' => array('CC6600', 'FFFBD0', 'FF9900', 'C13100'),
                'army' => array('595F23', '829F53', 'A2B964', '5F1E02', 'E15417', 'FCF141'),
                'pastel' => array('EF597B', 'FF6D31', '73B66B', 'FFCB18', '29A2C6'),
                'red' => array('FFFF66', 'FFCC00', 'FF9900', 'FF0000'),
            );
            $presetPalette = $params->get('wordlePresetPalette', 'aqua');
            $selectedPalette = $palettes[$presetPalette];

            $palette = Palette::get_palette_from_hex($cloud->get_image(),
                $selectedPalette
            );
            return $palette;
        }
    }

    public function getAllTags()
    {
        $results = $this->getModel("allTags");
        return $results;
    }

    private function getModel($paramPrefixName)
    {
        $app = JFactory::getApplication();
        $params = $app->getParams();

        $document = JFactory::getDocument();
        $cedTagsHelper = new CedTagsHelper();

        $description = CedTagsHelper::param($paramPrefixName . 'MetaDescription');
        $document->setDescription($cedTagsHelper->truncate($description));

        $keywords = CedTagsHelper::param($paramPrefixName . 'MetaKeywords');
        $document->setMetadata($paramPrefixName . 'Keywords', $keywords);

        $title = CedTagsHelper::param($paramPrefixName . 'Title');
        $document->setTitle($title);

        $count = intval($params->get($paramPrefixName . 'Count', 25));
        $sorting = $params->get('wordleTagOrder', 'sizeasort');
        $reverse = intval($params->get($paramPrefixName . 'Reverse', 1));

        $rows = $cedTagsHelper->getPopularTagModel($count, $sorting, $reverse);
        return $rows;
    }


}
