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
    var $cedTagModelTag = null;


    function __construct()
    {
        parent::__construct();
        $this->cedTagModelTag = new CedTagModelTag();
    }


    public function publish($articles)
    {

    }

    public function unpublish($articles)
    {

    }


    /**
     * Remove Tag xxxx to all articles which have also Tag yyyy
     */
    public function remove($tagX, $tagY)
    {
        $idTagX = $this->cedTagModelTag->getTagIdForTagName($tagX);
        $idTagY = $this->cedTagModelTag->getTagIdForTagName($tagY);

        if (!isset($idTagX)) {
            return JText::sprintf('Tag "%1$s" do not exist, please enter a valid tag xxxx name', (string)$tagX);
        }
        if (!isset($idTagY)) {
            return JText::sprintf('Tag "%1$s" do not exist, please enter a valid tag yyyy name', (string)$tagY);
        }

        $articleCounter = $this->cedTagModelTag->removeTagXToAllArticlesWithTagY($idTagX, $idTagY);

        return JText::sprintf('Success, tag "%1$s" has been removed to %2$s articles where tag name "%3$s" was present.', (string)$tagX, (string)$articleCounter, (string)$tagY);
    }


    /**
     * Add Tag yyyy to all articles which have also Tag xxxx
     *
     * @param $tagX id of tag X
     * @param $tagY id of tag Y
     */
    public function add($tagX, $tagY, $tagYWeigtht, $tagYDescription)
    {
        $idTagX = $this->cedTagModelTag->getTagIdForTagName($tagX);
        $idTagY = $this->cedTagModelTag->getTagIdForTagName($tagY);

        if (!isset($idTagX)) {
            return JText::sprintf('Tag "%1$s" do not exist, please enter a valid tag name', (string)$tagX);
        }

        $message = "";
        if ($idTagY == 0) {
            $message .= JText::sprintf('Tag "%1$s" was not existing and has been created', (string)$tagY);
            $idTagY = $this->cedTagModelTag->insertNewTag($tagY, $tagYDescription, $tagYWeigtht);
        }

        $this->cedTagModelTag->addTagYToAllArticlesWithTagX($idTagX, $idTagY);

        return JText::sprintf('Success, did add Tag "%2$s" to all articles with Tag "%1$s". ', $tagX, $tagY).$message;
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
        $idTagX = intval($this->cedTagModelTag->getTagIdForTagName($tagX));
        $idTagY = intval($this->cedTagModelTag->getTagIdForTagName($tagY));

        if ($idTagX == 0) {
            return JText::sprintf('Tag "%1$s" do not exist, please enter a valid tag to be replaced by tag "%2$s"', (string)$tagX, (string)$tagY);
        }
        if ($idTagY == 0) {
            $message = JText::sprintf('Tag "%1$s" was not existing and has been created.', (string)$tagY);
            $idTagY = $this->cedTagModelTag->insertNewTag($tagY, $tagYDescription, $tagYWeigtht);
        }

        $this->cedTagModelTag->replaceTag($idTagX, $idTagY);
        return JText::sprintf('Success, did replace Tag "%1$s" with Tag "%2$s" in all articles. ', $tagX, $tagY) . $message.  JText::sprintf('Tag "%1$s" has not been deleted.', $tagX);
    }


}