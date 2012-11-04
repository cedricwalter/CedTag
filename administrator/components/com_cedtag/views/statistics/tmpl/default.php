<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addScript("https://www.google.com/jsapi");

$document->addScriptDeclaration("

      google.load('visualization', '1', {packages:['corechart']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var terms = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['published',     " . $this->statistics->termPublished . "  ],
          ['unpublished',   " . $this->statistics->termUnpublished . "]
        ]);

        var optionsTerms = {
          title: 'Terms'
        };
        var chartTerms = new google.visualization.PieChart(document.getElementById('chart_terms'));
        chartTerms.draw(terms, optionsTerms);

        var description = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['With Description',     " . $this->statistics->termPublishedWithDescription . "  ],
          ['Without Description',   " . $this->statistics->termPublishedWithoutDescription . "]
        ]);

        var chartDescription = new google.visualization.PieChart(document.getElementById('chart_description'));
        chartDescription.draw(description, optionsTerms);

        var articles = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['with tags',     " . $this->statistics->articlesWithTags . "  ],
          ['without tags',   " . $this->statistics->articlesWithoutTags . "]
        ]);

        var optionsArticles = {
          title: 'Articles'
        };

        var chartArticles = new google.visualization.PieChart(document.getElementById('chart_articles'));
        chartArticles.draw(articles, optionsArticles);
      }
");
?>

<form action="<?php echo JRoute::_('index.php?controller=statistics&option=com_cedtag'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
    <div>
        <div id="chart_terms" style="float:left; width: 450px; height: 450px;"></div>
        <div id="chart_description" style="float:left; width: 450px; height: 450px;"></div>
        <div id="chart_articles" style="float:left; width: 450px; height: 450px;"></div>

    </div>
    <input type="hidden" name="controller" value="statistics">
    <?php echo JHTML::_('form.token'); ?>

</form>