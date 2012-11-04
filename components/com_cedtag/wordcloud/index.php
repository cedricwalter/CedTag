<?php

die('Restricted access only for testing');


define('_JEXEC', 1);

require dirname(__FILE__) . '/tagcloud.php';

$full_text = <<<EOT
With the release of Joomla! 2.5, the user community has placed a large emphasis on making the CMS installation and updates management process as simple and straightforward as possible.

This will allow users to more easily and frequently migrate to the latest version of Joomla!, and take advantage of all the security benefits associated with running the newest code. We think Joomla! users around the world will really embrace this new process.
The automated notification of Joomla! core and extension updates is a new built-in feature that simplifies site maintenance and management of updates:

- Available as a quick button on the administrative control panel of your Joomla! website, it enables administrators to update a site to the latest stable Joomla! release with just a single click.
- A second quick button handles updates for Joomla! extensions.
A major usability enhancement, this auto-updating feature eliminates the need to manually transfer and replace files on the server, converting a time-consuming, error-prone process into a seamless, effective, and time-saving experience.
EOT;

/*
$full_text =
    array(
        array('word' => "cedric", 'count' => "0", 'title' => "cedric", 'link' => "cedric"),
        array('word' => "cedric", 'count' => "0", 'title' => "cedric", 'link' => "cedric"),
        array('word' => "cedric", 'count' => "0", 'title' => "cedric", 'link' => "cedric"),
        array('word' => "cedric", 'count' => "0", 'title' => "cedric", 'link' => "cedric"),
        array('word' => "aaaa", 'count' => "0", 'title' => "cedric", 'link' => "cedric"),
        array('word' => "bbbbb", 'count' => "8", 'title' => "cedric", 'link' => "cedric"),
        array('word' => "bbbbb", 'count' => "0", 'title' => "cedric", 'link' => "cedric"),
        array('word' => "bbbbb", 'count' => "0", 'title' => "cedric", 'link' => "cedric"),
        array('word' => "bbbbb", 'count' => "5", 'title' => "cedric", 'link' => "cedric"),
        array('word' => "rrrrrr", 'count' => "2", 'title' => "cedric", 'link' => "cedric"),

    );
*/

$palettes = array(
    'aqua' => array('BED661', '89E894', '78D5E3', '7AF5F5', '34DDDD', '93E2D5'),
    'yellow/blue' => array('FFCC00', 'CCCCCC', '666699'),
    'grey' => array('87907D', 'AAB6A2', '555555', '666666'),
    'brown' => array('CC6600', 'FFFBD0', 'FF9900', 'C13100'),
    'army' => array('595F23', '829F53', 'A2B964', '5F1E02', 'E15417', 'FCF141'),
    'pastel' => array('EF597B', 'FF6D31', '73B66B', 'FFCB18', '29A2C6'),
    'red' => array('FFFF66', 'FFCC00', 'FF9900', 'FF0000'),
);


$font = dirname(__FILE__) . '/Arial.ttf';
$width = 600;
$height = 600;
//$width, $height, $font, $text = null, $imagecolor = array(0, 0, 0, 127), $words_limit = null, $vertical_freq = FrequencyTable::WORDS_MAINLY_HORIZONTAL
$cloud = new WordCloud(16, 72, $width, $height, $font, $full_text);


$palette = Palette::get_palette_from_hex($cloud->get_image(), $palettes['grey'], 0, FrequencyTable::WORDS_MAINLY_HORIZONTAL);
$cloud->render($palette);

// Render the cloud in a temporary file, and return its base64-encoded content
$file = tempnam(getcwd(), 'img');
imagepng($cloud->get_image(), $file);
$img64 = base64_encode(file_get_contents($file));
unlink($file);
imagedestroy($cloud->get_image());
?>

<img usemap="#mymap" src="data:image/png;base64,<?php echo $img64 ?>" border="0"/>
<map name="mymap">
    <?php foreach ($cloud->get_image_map() as $map): ?>
    <area shape="rect" coords="<?php echo $map[1]->get_map_coords() ?>" onclick="alert('You clicked: <?php echo $map[0] ?>');"/>
    <?php endforeach ?>
</map>
