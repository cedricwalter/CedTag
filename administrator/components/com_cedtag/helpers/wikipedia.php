<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.error.log');

class CedTagWikipedia extends JObject
{

    var $wikipediaServer = 'http://en.wikipedia.org';

    //some variables for statistics purposes
    var $requested = 0;
    var $found = 0;
    var $notFound = array();

    function __construct()
    {
        parent::__construct();
        $this->wikipediaServer = CedTagsHelper::param('wikipediaServer', 'http://en.wikipedia.org');
    }

    public function getDefinition($searchTerm)
    {
        $this->setRequested($this->getRequested() + 1);

        $description = $this->getDefinitionFrom($searchTerm, $this->wikipediaServer);

        if (!is_array($description)) {
            $notFound = $this->getNotFound();
            $notFound[] = $searchTerm;
        } else {
            $this->setFound($this->getFound() + 1);
        }

        return $description;
    }


    /**
     * @param $searchTerm
     * @param string $wikipediaServer
     * @return array|string  [text, description, url]
     */
    public function getDefinitionFrom($searchTerm, $wikipediaServer = 'http://en.wikipedia.org')
    {
        $url = $wikipediaServer .
            '/w/api.php?action=opensearch&search='
            . urlencode($searchTerm) . '&format=xml&limit=1';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_REFERER, "");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; he; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8");
        $page = curl_exec($ch);
        $xml = simplexml_load_string($page);
        if ((string)$xml->Section->Item->Description) {
            return array((string)$xml->Section->Item->Text,
                (string)$xml->Section->Item->Description,
                (string)$xml->Section->Item->Url);

        }
        return "";
    }

    public function setWikipediaServer($wikipediaServer)
    {
        $this->wikipediaServer = $wikipediaServer;
    }

    public function getWikipediaServer()
    {
        return $this->wikipediaServer;
    }

    public function setRequested($requested)
    {
        $this->requested = $requested;
    }

    public function getRequested()
    {
        return $this->requested;
    }

    public function setFound($found)
    {
        $this->found = $found;
    }

    public function getFound()
    {
        return $this->found;
    }

    public function setNotFound($notFound)
    {
        $this->notFound = $notFound;
    }

    public function getNotFound()
    {
        return $this->notFound;
    }


}
