<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Blossom\Classes\Url;

$url = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

$terms = $this->person->getTerms();
if (count($terms)) {
	echo '<ul class="tabBar">';
	$tabs = ['committees'  => $this->_(['labels.committee', 'labels.committees', 2]),
			 'votingRecord'=> $this->_(['labels.votingRecord', 'labels.votingRecords', 1]),
			 'comparisons' => $this->_('labels.votingRecordComparison')];
	$current_tab = (isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs)) ? $_GET['tab'] : 'committees';

	foreach ($tabs as $tab => $label) {
		$class = $current_tab == $tab ? 'class="current"' : '';
		$url->tab = $tab;
		echo "<li $class><a href=\"$url\">$label</a></li>";
	}
	echo '</ul>';

	include APPLICATION_HOME."/blocks/html/people/$current_tab.inc";
}
