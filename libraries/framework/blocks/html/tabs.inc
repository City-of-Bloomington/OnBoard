<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->tabs
 * @param string $this->current_tab
 */
$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

echo "<ul class=\"tabBar\">";
foreach ($this->tabs as $tab=>$title) {
	$class = $this->current_tab == $tab ? 'class="current"' : '';
	$name = View::escape($title);
	$url->tab = $tab;
	echo "<li><a href=\"$url\" $class>$name</a></li>";
}
echo "</ul>";
