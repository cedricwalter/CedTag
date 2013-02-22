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


class CedTagControllerStatistics extends JController
{
    function __construct()
    {
        parent::__construct();
    }

    public function execute($task)
    {
        switch ($task) {
            default:
                $this->display();
        }
    }

    /**
     * @param bool $cachable
     * @param bool $urlparams
     * @return JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
        JFactory::getApplication()->input->set('view', 'statistics');
        parent::display();
    }

}
