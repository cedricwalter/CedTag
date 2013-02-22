<?php
/**
 * @package Plugin cedAddTags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');
require_once JPATH_SITE . '/components/com_cedtag/helpers/themes.php';

class plgButtonCedTagAdd extends JPlugin
{

    /**
   	 * Constructor
   	 *
   	 * @access      protected
   	 * @param       object  $subject The object to observe
   	 * @param       array   $config  An array that holds the plugin configuration
   	 * @since       1.5
   	 */
   	public function __construct(& $subject, $config)
   	{
   		parent::__construct($subject, $config);
   		$this->loadLanguage();
   	}

    /**
     * Add Attachment button
     *
     * @return a button
     */
    function onDisplay($name, $asset, $author)
    {
        // Avoid displaying the button for anything except content articles
        $option = JRequest::getVar('option');
        if ($option != 'com_content') {
            return new JObject();
        }

        // Get the article ID
        $cid = JRequest::getVar('cid', array(0), '', 'array');
        $id = 0;
        if (count($cid) > 0) {
            $id = intval($cid[0]);
        }
        if ($id == 0) {
            $nid = JRequest::getVar('id', null);
            if (!is_null($nid)) {
                $id = intval($nid);
            }
        }

        JHtml::_('behavior.modal');

        // Create the button object
        $button = new JObject();

        // Figure out where we are and construct the right link and set
        // up the style sheet (to get the visual for the button working)
        $document = & JFactory::getDocument();
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            $document->addStyleSheet(JURI::root() . '/media/com_cedtag/css/admintag.css');

            if ($id == 0) {
                $button->set('options', "{handler: 'iframe', size: {x: 400, y: 300}}");
                $link = "index.php?option=com_cedtag&controller=tag&amp;task=warning&amp;tmpl=component&amp;tagsWarning=FIRST_SAVE_WARNING";
            }
            else {
                $button->set('options', "{handler: 'iframe', size: {x: 600, y: 300}}");
                $link = "index.php?option=com_cedtag&amp;controller=tag&amp;task=add&amp;article_id=" . $id . "&amp;tmpl=component";
            }
        }
        else {
            $CedTagThemes = new CedTagThemes();
            $CedTagThemes->addCss();

            //return $button;
            if ($id == 0) {
                $button->set('options', "{handler: 'iframe', size: {x: 400, y: 300}}");
                $msg = JText::_('SAVE ARTICLE BEFORE ADD TAGS');
                $link = "index.php?option=com_cedtag&amp;task=warning&amp;tmpl=component&amp;tagsWarning=FIRST_SAVE_WARNING";
            }
            else {
                $button->set('options', "{handler: 'iframe', size: {x: 500, y: 300}}");
                $link = "index.php?option=com_cedtag&amp;tmpl=component&amp;task=add&amp;article_id=" . $id;
            }
        }

        $button->set('modal', true);
        $button->set('class', 'modal');
        $button->set('text', JText::_('Add Tags'));
        $button->set('name', 'add_Tags');
        $button->set('link', $link);
        //$button->set('image', '');

        return $button;
    }
}

?>
