<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL GNU General Public License version 3 or later; see LICENSE.txt
 **/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.application.input');

// userhelper for acl
require_once JPATH_SITE . '/administrator/components/com_users/helpers/users.php';

/**
 * View class for a list of articles.
 *
 * @package        Joomla.Administrator
 * @subpackage    com_content
 * @since        1.6
 */
class cedtagViewtag extends JView
{
    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null)
    {
        $layout = JFactory::getApplication()->input->get('layout', "default", 'STRING');
        switch ($layout) {
            case 'add':
                $this->add($tpl);
                break;
            case 'warning':
                $this->warning($tpl);
                break;
            default:
                $this->defaultTpl($tpl);
        }
    }

    /**
     * @param null $tpl
     * @throws Exception
     */
    public function defaultTpl($tpl = null)
    {
        $cedtagparams = JComponentHelper::getParams('com_cedtag');
        $this->assignRef('cedtagparams', $cedtagparams);

        //get data
        //$tagList =& $this->get('tagList');
        $items = $this->get('Items');

        //combine Joomla items with tagslist model, i dont know when joomla cms do the sql call so i do it here, a bit late and not in the model doh!
        $this->items = $this->addTagsToJoomlaArticlesModel($items);


        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->authors = $this->get('Authors');


        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        // Levels filter.
        $options = array();
        $options[] = JHtml::_('select.option', '1', JText::_('J1'));
        $options[] = JHtml::_('select.option', '2', JText::_('J2'));
        $options[] = JHtml::_('select.option', '3', JText::_('J3'));
        $options[] = JHtml::_('select.option', '4', JText::_('J4'));
        $options[] = JHtml::_('select.option', '5', JText::_('J5'));
        $options[] = JHtml::_('select.option', '6', JText::_('J6'));
        $options[] = JHtml::_('select.option', '7', JText::_('J7'));
        $options[] = JHtml::_('select.option', '8', JText::_('J8'));
        $options[] = JHtml::_('select.option', '9', JText::_('J9'));
        $options[] = JHtml::_('select.option', '10', JText::_('J10'));

        $this->assign('f_levels', $options);

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
        }

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since    1.6
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(JText::_('Tag Manager'), 'tag.png');

        $canDo = UsersHelper::getActions();
        if ($canDo->get('core.create')) {
            //add new
        }
        if ($canDo->get('core.edit')) {
            //edit
        }
        if ($canDo->get('core.edit.state')) {
            //publish / unpublish / archiveList / checkin
        }
        if ($canDo->get('core.delete')) {
            $bar = JToolBar::getInstance('toolbar');
            $bar->appendButton('Confirm', JText::_('Remove all Tags from all articles?'), 'clearall', JText::_('Clear all'), 'clearall', false);
            JToolBarHelper::spacer();
            JToolBarHelper::divider();
        }
        if ($canDo->get('core.edit.state')) {
            //trash
        }
        if ($canDo->get('core.admin')) {
            //preference
        }

        JToolBarHelper::back(JText::_('CEDTAG_CONTROL_PANEL'), 'index.php?option=com_cedtag');
    }

    function add($tpl = null)
    {
        $tags = $this->get('tagsForArticle');
        $tagit = explode(",", $tags);
        $this->assignRef('tags', $tagit);
        parent::display($tpl);
    }

    function warning($tpl = null)
    {
        parent::display($tpl);
    }


    function addTagsToJoomlaArticlesModel($items)
    {
        //not pretty to refetch model in view instead of in model!
        $dbo = JFactory::getDbo();
        foreach ($items as $item) {
            $query = "select tagterm.id,tagterm.name from #__cedtag_term as tagterm
                                left join #__cedtag_term_content as tagtermcontent
                                on tagtermcontent.tid=tagterm.id
                                where tagtermcontent.cid=" . $dbo->quote($item->id) . "
                                and tagterm.published='1'
                                group by(tid)
                                order by tagterm.weight
                                desc,tagterm.name";
            $dbo->setQuery($query);
            $tags = $dbo->loadObjectList();

            $tagit = array();
            if ($tags != null) {
                foreach ($tags as $tag) {
                    $tagit[] = $tag->name;
                }
            }
            $item->tag = implode(",", $tagit);
            $item->tagit = $tagit;

            $item->tagid = $item->id;
        }

        return $items;
    }

}
