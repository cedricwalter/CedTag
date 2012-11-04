<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.application.pathway');
jimport('joomla.application.input');
require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';

class CedTagViewTag extends JView
{
    /**
     * @param array $config
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * @param null $tpl
     * @return mixed|void
     */
    function display($tpl = null)
    {
        $layout = JFactory::getApplication()->input->get('layout', null, 'string');
        switch ($layout) {
            case 'warning':
                $this->warning($tpl);
                break;
            default:
                $this->defaultTpl($tpl);
        }
    }

    /**
     * @param null $tpl
     * @return mixed
     */
    private function defaultTpl($tpl = null)
    {
        $params = JComponentHelper::getParams('com_cedtag');

        //Layout
        $defaultLayout = $params->get('layout', 'blog');
        $layout = JFactory::getApplication()->input->get('layout', $defaultLayout, 'STRING');
        $this->setLayout($layout);

        $tag = JFactory::getApplication()->input->get('tag', null, 'string');

        // create custom pathway
        $app = JFactory::getApplication();
        $pathway = $app->getPathway();
        $pathway->addItem(JText::_('COM_CEDTAG_PATHWAY_TITLE'), JURI::root() . 'index.php?option=com_cedtag&view=alltags');
        $pathway->addItem($tag, '');

        $cedTagsHelper = new CedTagsHelper();
        $tag = $cedTagsHelper->unUrlTagname($tag);
        JFactory::getApplication()->input->set('tag', $tag);
        $this->assign('tag', $tag);

        //Term do not exist
        $isTermExist = $this->get('TermExist');
        if (!$isTermExist) {
            $tagNotFoundUseWarningPage = CedTagsHelper::param('tagNotFoundUseWarningPage', true);

            if ($tagNotFoundUseWarningPage) {
                $this->assign('message', 'REQUEST_TAG_NOT_EXIST_WARNING');
                $this->setLayout('requested_tag_do_not_exist');
                return parent::display($tpl);
            }

            JError::raiseError(404, JText::_("Could not find tag \"$tag\""));
        }

        //No Articles foudn with that Term
        $hasTermArticles = $this->get('TermArticles');
        if (!$hasTermArticles) {
            $this->assign('message', 'NO_ARTICLES_WITH_TAG');
            $this->setLayout('no_articles_with_tag');
            return parent::display($tpl);
        }

        $items = $this->get('Data');
        $this->assignRef('results', $items);
        //switch off edit capabilities to reuse blog_item.php
        $comContentParams = clone JComponentHelper::getParams('com_content');

        //TODO not correct for sure for blog with editor
        $comContentParams->set('access-edit', false);
        $comContentParams->set('access-view', true);

        foreach ($items as $item) {
            $item->params = $comContentParams;

            //TODO switch this off for now
            $item->alternative_readmore = null;
        }

        $this->prepareData($items);

        $this->assign('total', $this->get('Total'));

        $this->assign('tagDescription', $this->get('TagDescription'));
        $this->assign('showDescription', $params->get('description', '1'));

        $this->assign('showMeta', $params->get('contentMeta', '1'));

        $this->assignRef('ads', $params->get('ads', ''));

        $this->assignRef('pagination', $this->get('Pagination'));
        $this->assign('show_pagination', $comContentParams->def('show_pagination', 1));
        $this->assign('show_pagination_results', $comContentParams->def('show_pagination_results', 1));

        $this->assign('ads_top_use', $params->get('ads_top_use', '0'));
        $this->assign('ads_top_content', $params->get('ads_top_content', '0'));

        $this->assign('ads_bottom_use', $params->get('ads_bottom_use', '0'));
        $this->assign('ads_bottom_content', $params->get('ads_bottom_content', '0'));

        parent::display($tpl);

    }


    /**
     * Execute onContentAfterTitle/onContentBeforeDisplay/onContentAfterDisplay all content plugin on introtext
     *
     * Method extracted from file /components/com_content/views/category/view.html.php
     * partially part of method display($tpl = null)
     * because it is not reusable from Joomla!
     *
     * @param $items
     */
    private function prepareData($items)
    {
        $params = JComponentHelper::getParams('com_content');

        // PREPARE THE DATA
        // Get the metrics for the structural page layout.
        $numLeading = $params->def('num_leading_articles', 1);
        $numIntro = $params->def('num_intro_articles', 4);
        $numLinks = $params->def('num_links', 4);

        // Compute the article slugs and prepare introtext (runs content plugins).
        for ($i = 0, $n = count($items); $i < $n; $i++) {
            $item = &$items[$i];
            $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

            // No link for ROOT category
            if ($item->parent_alias == 'root') {
                $item->parent_slug = null;
            }

            $item->event = new stdClass();

            $dispatcher = JDispatcher::getInstance();

            // Ignore content plugins on links.
            if ($i < $numLeading + $numIntro) {
                $item->introtext = JHtml::_('content.prepare', $item->introtext, '', 'com_content.category');

                $results = $dispatcher->trigger('onContentAfterTitle', array('com_content.article', &$item, &$item->params, 0));
                $item->event->afterDisplayTitle = trim(implode("\n", $results));


                $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_content.article', &$item, &$item->params, 0));
                $item->event->beforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentAfterDisplay', array('com_content.article', &$item, &$item->params, 0));
                $item->event->afterDisplayContent = trim(implode("\n", $results));
            } else {
                $item->event->afterDisplayTitle = '';
                $item->event->beforeDisplayContent = '';
                $item->event->afterDisplayContent = '';
            }
        }
    }


    private function warning($tpl = null)
    {
        parent::display($tpl);
    }


}
