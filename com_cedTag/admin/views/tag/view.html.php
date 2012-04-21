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
jimport( 'joomla.application.input' );

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
        $layout = JInput::get('layout', "default", 'STRING');
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
     * Display the view
     *
     * @return    void
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
            JError::raiseError(500, implode("\n", $errors));
            return false;
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
        JToolBarHelper::custom('batchsave', 'save', '', JText::_('SAVE'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::custom('clearall', 'delete', '', JText::_('CLEAR ALL'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::back(JText::_('CONTROL PANEL'), 'index.php?option=com_cedtag');
    }

    function add($tpl = null)
    {
        $tags =& $this->get('tagsForArticle');
        $this->assignRef('tags', $tags);
        parent::display($tpl);
    }

    function warning($tpl = null)
    {
        parent::display($tpl);
    }


    function addTagsToJoomlaArticlesModel($items) {
        //not pretty to refetch model in view instead of in model!
        $dbo = JFactory::getDbo();
        foreach ($items as $item) {
            $query = "select tagterm.id,tagterm.name from jos_cedtag_term as tagterm
                                left join jos_cedtag_term_content as tagtermcontent
                                on tagtermcontent.tid=tagterm.id
                                where tagtermcontent.cid=".$dbo->quote($item->id)."
                                and tagterm.published='1'
                                group by(tid)
                                order by tagterm.weight
                                desc,tagterm.name";
            $dbo->setQuery($query);
            $tags = $dbo->loadObjectList();

            $tagname = array();
            if ($tags != null) {
                foreach ($tags as $tag) {
                    $tagname[] = $tag->name;
                }
            }
            $item->tag = implode(",", $tagname);
            $item->tagid = $item->id;
        }

        return $items;
    }

}
