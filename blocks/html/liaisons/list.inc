<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 * @param boolean $this->disableButtons
 */
use Application\Models\Committee;
use Application\Models\Liaison;
use Blossom\Classes\Url;

$title = $this->_(['liaison', 'liaisons', count($this->data)]);

$helper = $this->template->getHelper('buttonLink');

$tools = '';
foreach (Liaison::$types as $type) {
    $tools.= $helper->buttonLink(
        BASE_URI.'/liaisons?type='.$type,
        $this->_($type)
    );
}
$uri = new Url(Url::current_url(BASE_HOST));
$uri->format = 'csv';
$tools.= $helper->buttonLink($uri, 'csv', 'download');

$uri->format = 'email';
$tools.= $helper->buttonLink($uri, 'email', 'mail');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
        <nav class="tools"><?= $tools; ?></nav>
    </header>
    <?php
        $this->disableButtons = true;
        $this->_include('liaisons/partials/dataTable.inc');
    ?>
</section>
