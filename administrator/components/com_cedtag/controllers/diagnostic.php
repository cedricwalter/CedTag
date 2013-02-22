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


class CedTagControllerDiagnostic extends JController
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
     * @param bool $cacheable
     * @param bool $urlParams
     * @return JController|void
     */
    public function display($cacheable = false, $urlParams = false)
    {
        JFactory::getApplication()->input->set('view', 'diagnostic');
        parent::display();
    }

    public function toggle()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->update('#__extensions');
        $query->set('enabled=1');
        $query->where("element like '%tag%'");

        $dbo->setQuery($query);
        $dbo->query();
    }


}
