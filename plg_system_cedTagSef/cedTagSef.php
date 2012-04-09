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


class plgSystemCedTagSef extends JPlugin
{
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
    }

    public function onAfterInitialise()
    {
        $app =& JFactory::getApplication();
        if ($app->getName() != 'site') {
            return true;
        }
        $uir = $_SERVER['REQUEST_URI'];
        //$uir	= JURI::getInstance()->;

        if (strpos($uir, '/tag/index.php') !== false) {
            return true;
        }
        if (strpos($uir, '/tag/') !== false && strpos($uir, '/component/tag/') === false) {
            $_SERVER['REQUEST_URI'] = str_replace('/tag/', '/component/tag/', $uir);
            $this->prehandle($uir);

        } else if (strpos($uir, 'tag/') === 0) {
            $_SERVER['REQUEST_URI'] = str_replace('tag/', 'component/tag/', $uir);
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
        JRequest::setVar('tag', $tag);
        JRequest::setVar('option', 'com_cedtag');
    }

    /**
     * Converting the site URL to fit to the HTTP request
     */
    public function onAfterRender()
    {
        $app =& JFactory::getApplication();

        if ($app->getName() != 'site') {
            return true;
        }
        $buffer = JResponse::getBody();
        $regex = '#component/tag/#m';
        $buffer = preg_replace($regex, 'tag/', $buffer);
        JResponse::setBody($buffer);
        return true;
    }


}
