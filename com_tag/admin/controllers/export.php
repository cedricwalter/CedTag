<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die();

class TagControllerExport extends JController
{

    function __construct()
    {
        parent::__construct();
    }

    function execute($task)
    {
        switch ($task) {
            case 'export':
                $this->import();
                break;
            default:
                $this->display();
        }

    }

    /**
     * display the form
     * @return void
     */
    function display()
    {
        JRequest::setVar('view', 'import');
        parent::display();
    }


    function export()
    {
        /*
         * First create a temp table:

    CREATE TABLE tmpcontent (cid INTEGER(11) UNSIGNED NOT NULL, metakey TEXT NOT NULL);

Fill it with the metadata:



    INSERT INTO tmpcontent SELECT c.id, GROUP_CONCAT(t.name SEPARATOR ',') FROM jos_content AS c LEFT JOIN jos_tag_term_content AS t2c ON t2c.cid=c.id LEFT JOIN jos_tag_term AS t ON t.id=t2c.tid GROUP BY c.id;



Have a look at the temp table to ensure info is correct:



    SELECT * FROM tmpcontent;



Copy the metadata to the content:



    UPDATE jos_content AS c, tmpcontent AS t SET c.metakey=t.metakey WHERE c.id=t.cid;

         */


    }

}

?>
