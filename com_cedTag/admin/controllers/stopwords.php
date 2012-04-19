<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/


defined('_JEXEC') or die();
jimport( 'joomla.application.input' );
jimport( 'joomla.filesystem.file' );

class CedTagControllerStopWords extends JController
{

    function execute($task)
    {
        switch ($task) {
            case 'save':
                $this->save();
                break;
            case 'restore':
                $this->restore();
                break;

            default:
                $this->display();
        }
    }

    function display()
    {
        JFactory::getApplication()->input->set('view', 'stopwords');
        parent::display();
    }

    function save()
    {
        $updatedFileContent = JFactory::getApplication()->input->get('content', '', 'STRING');

        $lang = JFactory::getLanguage()->getDefault();
        $file = JPATH_ADMINISTRATOR.'/components/com_cedtag/stopwords/stopwords_'.$lang.'.php';
        JFile::write($file, trim($updatedFileContent));

        JFactory::getApplication()->input->set('view', 'stopwords');
        parent::display();
    }

    function restore()
    {
        $lang = JFactory::getLanguage()->getDefault();
        $defaultfile = JPATH_ADMINISTRATOR.'/components/com_cedtag/stopwords/stopwords_'.$lang.'-default.php';
        $file = JPATH_ADMINISTRATOR.'/components/com_cedtag/stopwords/stopwords_'.$lang.'.php';
        JFile::copy($defaultfile, $file);

        JFactory::getApplication()->input->set('view', 'stopwords');
        parent::display();
    }
}
?>