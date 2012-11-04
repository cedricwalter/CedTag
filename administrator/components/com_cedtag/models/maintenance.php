<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/tag.php';

class CedTagModelMaintenance extends CedTagModelTag
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Remove Tag xxxx to all articles which have also Tag yyyy
     */
    public function remove($tagX, $TagY)
    {
        $idTagX = $this->getTagIdForTagName($tagX);
        $idTagY = $this->getTagIdForTagName($TagY);

        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $cIds = $this->getContentIdsForTagId($idTagY);
        foreach ($cIds as $cId) {
            $query->clear();
            $query->delete('#__cedtag_term_content');
            $query->where('tid =' . $dbo->quote($idTagX));
            $query->where('cid =' . $dbo->quote($cId));
            $dbo->setQuery($query);
            $dbo->query();
        }

        return JText::_("SUCCESS");
    }


    /**
     * Add Tag yyyy to all articles which have also Tag xxxx
     *
     * @param $tagX id of tag X
     * @param $tagY id of tag Y
     */
    public function add($tagX, $tagY)
    {
        $idTagX = $this->getTagIdForTagName($tagX);
        $idTagY = $this->getTagIdForTagName($tagY);

        if (!isset($tagX)) {
            $message = 'Tag "%2$s" was not existing and has been created';

            $idTagY = $CedTagModelTag->insertNewTag(strval($tagY), intval($tagYWeigtht), strval($tagYDescription));
        }

        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $cIds = $this->getContentIdsForTagId($idTagX);

        foreach ($cIds as $cid) {
            $query->clear();
            $query->insert('#__cedtag_term_content');
            $query->columns(array($dbo->quoteName('tid'), $dbo->quoteName('cid')));
            $query->values($idTagY . ',' . $cid);
            $dbo->setQuery($query);
            $dbo->query();
        }

        return JText::_("SUCCESS");
    }


    /**
     * Replace Tag xxxx with Tag yyyy in all articles
     *
     * @param $tagX id of tag X
     * @param $tagY id of tag Y
     * @param $tagYWeigtht
     * @param $tagYDescription
     * @return string
     */
    public function replace($tagX, $tagY, $tagYWeigtht, $tagYDescription)
    {
        $CedTagModelTag = new CedTagModelTag();

        $idTagX = intval($this->getTagIdForTagName("zzzzzzzzz"));
        $idTagY = intval($this->getTagIdForTagName($tagY));

        if ($idTagX == 0) {
            return JText::sprintf('Tag "%1$s" do not exist, please enter a valid tag to be replaced by tag "%2$s"', (string)$tagX, (string)$tagY);
        }
        if ($idTagY == 0) {
            $message = 'Tag "%2$s" was not existing and has been created';

            $idTagY = $CedTagModelTag->insertNewTag(strval($tagY), intval($tagYWeigtht), strval($tagYDescription));
        }

        $CedTagModelTag->replaceTag($idTagX, $idTagY);

        return JText::_("Success").$message;
    }



    /**
     *
     * @param $tagX name of tag
     * @return mixed
     */
    private function getTagIdForTagName($tagX)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('id as id');
        $query->from('#__cedtag_term');
        $query->where('name = ' . $dbo->quote($tagX));
        $dbo->setQuery($query);
        $idTag = $dbo->loadColumn();
        if (is_array($idTag)) {
            return $idTag[0];
        }
        return null;
    }

    /**
     * @param $idX
     * @return mixed
     */
    private
    function getContentIdsForTagId($idX)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('cid');
        $query->from('#__cedtag_term_content');
        $query->where('tid = ' . $dbo->quote($idX));
        $dbo->setQuery($query);
        $cIds = $dbo->loadColumn();
        if (is_array($cIds)) {
            return $cIds[0];
        }
        return null;
    }

}