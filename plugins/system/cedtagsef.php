<?php
/**
 * @package Plugin tagSef for Joomla! 2.5
 * @version $Id: tagSef.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
require_once (JPATH_SITE . '/components/com_cedtag/helpers/helper.php');
	
class plgSystemCedTagSef extends JPlugin
{
    var $sefTagBase = null;
    var $active = false;

    const PATH_COMPONENT_CEDTAG = '/component/cedtag/';

    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param    object        $subject The object to observe
     * @param     array          $config  An array that holds the plugin configuration
     * @since    1.0
     */
    public function plgSystemCedTagSef(&$subject, $config)
    {
        parent::__construct($subject, $config);
       $this->sefTagBase = CedTagsHelper::param('sefUrlBase', 'tag');        
    }

    public function onAfterInitialise()
    {
        $app = JFactory::getApplication();

        //No sef for backend site or if it is swicth off
        if ($app->getName() != 'site' || $app->getCfg('sef') == '0') {
            return true;
        }

        $uir = $_SERVER['REQUEST_URI'];

        if (strpos($uir, '/cedtag/index.php') !== false) {
            return true;
        }
        if (strpos($uir, '/' . $this->sefTagBase . '/') !== false && strpos($uir, self::PATH_COMPONENT_CEDTAG) === false) {
            $_SERVER['REQUEST_URI'] = str_replace('/' . $this->sefTagBase . '/', self::PATH_COMPONENT_CEDTAG, $uir);
            $this->prehandle($uir);

        } else if (strpos($uir, 'cedtag/') === 0) {
            $_SERVER['REQUEST_URI'] = str_replace('' . $this->sefTagBase . '/', 'component/cedtag/', $uir);
            $this->prehandle($uir);
        }
        return true;
    }

    private function prehandle($uir)
    {
        $lastSplash = strrpos($uir, '/');
        $tag = substr($uir, $lastSplash + 1);
        if (strpos($tag, '.')) {
            $tag = substr($tag, 0, strrpos($tag, '.'));
        }

        //http://docs.joomla.org/JInput_Background_for_Joomla_Platform
        $jInput = JFactory::getApplication()->input;
        $jInput->set('tag', $tag);
        $jInput->set('option', 'com_cedtag');
        $jInput->set('view', 'tag'); //avoid error 500
        $jInput->set('Itemid', 0); //To show only modules shown in all positions
    }

    /**
     * Converting the site URL to fit to the HTTP request
     */
    public function onAfterRender()
    {
        $app = JFactory::getApplication();
        if ($app->getName() != 'site' || $app->getCfg('sef') == '0') {
            return true;
        }

        $buffer = JResponse::getBody();
        $regex = '#component/cedtag/#m';
        $buffer = preg_replace($regex, '' . $this->sefTagBase . '/', $buffer);
        JResponse::setBody($buffer);
        return true;
    }
}