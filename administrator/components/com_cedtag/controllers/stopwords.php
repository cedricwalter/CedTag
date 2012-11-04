<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/


defined('_JEXEC') or die();
jimport('joomla.application.input');
jimport('joomla.filesystem.file');

require_once (JPATH_COMPONENT . '/controllers/file.php');

class CedTagControllerStopWords extends CedTagControllerFile
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @Override
     * @return string
     */
    public function getDefaultFile()
    {
        $lang = JFactory::getLanguage()->getDefault();
        $file = JPATH_ADMINISTRATOR . '/components/com_cedtag/stopwords/stopwords_' . $lang . '-default.php';
        return $file;
    }

    /**
     * @Override
     * @return string
     */
    public function getFile()
    {
        $lang = JFactory::getLanguage()->getDefault();
        $file = JPATH_ADMINISTRATOR . '/components/com_cedtag/stopwords/stopwords_' . $lang . '.php';
        return $file;
    }

    /**
     * @Override
     * @return string
     */
    public function getDefaultView()
    {
        return 'stopwords';
    }

}
