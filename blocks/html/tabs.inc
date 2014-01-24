<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->tabs
 * @param string $this->current_tab
 * @param URL $this->base_url
 */
use Blossom\Classes\Url;

$url = isset($this->base_url) ? $this->base_url : new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

echo "<ul class=\"tabBar\">";
foreach ($this->tabs as $tab) {
	$class = $this->current_tab == $tab ? 'class="current"' : '';
	$url->tab = $tab;

	// NOTE: we are cheating and creating singluar forms by just
	//	stripping the "s" off the words.
	//
	// Tab names will be plural forms, matching the database tables.
	// We know what all the database tables are, and they're simple.
	// We have pluralized entries in the en_US.po for each of these.
	$singular = substr($tab, 0, -1);
	$name = $this->_(["labels.$singular", "labels.$tab", 2]);
	echo "<li $class><a href=\"$url\">$name</a></li>";
}
echo "</ul>";
