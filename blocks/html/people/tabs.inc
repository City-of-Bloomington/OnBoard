<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
use Blossom\Classes\Url;

$url = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

$tabs = ['committees'  => $this->_(['committee', 'committees', 2])];
if (defined('VOTE_TRACKING') && VOTE_TRACKING) {
    $tabs['votingRecord'] = $this->_(['votingRecord', 'votingRecords', 1]);
    $tabs['comparisons' ] = $this->_('votingRecordComparison');
}
$current_tab = (isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs))
    ? $_GET['tab']
    : 'committees';

echo '<ul class="tabBar">';
foreach ($tabs as $tab => $label) {
    $class = $current_tab == $tab ? 'class="current"' : '';
    $url->tab = $tab;
    echo "<li $class><a href=\"$url\">$label</a></li>";
}
echo '</ul>';

include APPLICATION_HOME."/blocks/html/people/$current_tab.inc";
