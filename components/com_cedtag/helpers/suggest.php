<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

class CedTagSuggest extends JObject
{

    public function __construct()
    {
        parent::__construct();
    }


    public function addSiteJs($initialTags = "[]", $articleId)
    {
        $this->addJs($initialTags, $articleId, "", "");
    }


    public function addAdminJs($initialTags = "[]", $articleId, $tabindex = 1)
    {
        $this->addJs($initialTags, $articleId, "administrator/", "&controller=tag",$tabindex);
    }

    //http://jqueryui.com/demos/autocomplete/
    //http://webspirited.com/tagit/
    //http://jquery.webspirited.com/2011/02/jquery-tagit-a-jquery-tagging-plugin/
    //http://webspirited.com/tagit/?theme=simple-green#demos
    private function addJs($initialTags = "[]", $articleId, $area = "", $controller = "", $tabindex = 1)
    {
        $this->addStaticJavascript();

        $initialTags = implode("','", $initialTags);
        $articleIdParam = '&cid=' . $articleId;

        //instead of using a different alias for the jQuery object (when using noConflict),
        //I always write my jQuery code by wrapping it all in a closure. This can be done in the document.ready function:
        $script = "jQuery(function($) {
                    $('#tags" . $articleId . "').tagit({tagSource:'" . JURI::root() . $area . "index.php?option=com_cedtag" . $controller . "&" . JSession::getFormToken() . "=1&format=raw&task=ajaxSuggest',
                         select:true,
                         initialTags:['" . $initialTags . "'],
                         sortable:true,
                         maxTags:20,
                         tabindex:".$tabindex.",
                         triggerKeys:['enter', 'comma', 'tab'],
                         allowSpaces:" . (CedTagsHelper::param('spacesAllowedWithoutQuotes', 0) ? 'true' : 'false') . ",
                         highlightOnExistColor:'#0F0',
                         tagsChanged:function (tagValue, action, element) {
                            var xmlHttp =  new XMLHttpRequest();
                            if (action == 'moved') {

                            }
                            if (action == 'popped') {
                                url = '" . JURI::root() . $area . "index.php?option=com_cedtag" . $controller . "&" . JSession::getFormToken() . "=1&format=raw&task=ajaxDelete&tags=' + tagValue + '" . $articleIdParam . "';
                                xmlHttp.open( 'GET', url, false );
                                xmlHttp.send( null );
                            }
                            if (action == 'added') {
                                url = '" . JURI::root() . $area . "index.php?option=com_cedtag" . $controller . "&" . JSession::getFormToken() . "=1&format=raw&task=ajaxAdd&tags=' + tagValue + '" . $articleIdParam . "';
                                xmlHttp.open( 'GET', url, false );
                                xmlHttp.send( null );
                            }
                         }
                     }
                    );
                });";

        $document = JFactory::getDocument();
        $document->addScriptDeclaration($script);
    }

    public function addStaticJavascript()
    {
        // this will make sure mootools loads first to avoid jquery conflict issues
        JHTML::_('behavior.mootools');

        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . "/media/com_cedtag/css/jquery-ui-base-1.8.20.css");
        $document->addStyleSheet(JURI::root() . "/media/com_cedtag/css/tagit-stylish-yellow.css");

        $document->addScript(JURI::root() . "media/com_cedtag/js/jquery.1.7.2.min.js");
        $document->addScript(JURI::root() . "media/com_cedtag/js/jquery-ui.1.8.20.min.js");
        $document->addScript(JURI::root() . "media/com_cedtag/js/tagit.js");
        $document->addScriptDeclaration("jQuery.noConflict();");
    }
}