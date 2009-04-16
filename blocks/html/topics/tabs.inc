<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 * @param string $this->currentTab
 */
$tabs = array('legislation'=>$this->topic->getURL(),
			  'votes'=>BASE_URL.'/topics/votes.php?topic_id='.$this->topic->getId());
?>
<div class="tabBar">
	<ul>
	<?php
		foreach ($tabs as $tab=>$url) {
			$class = $this->currentTab==$tab ? 'class="current"' : '';
			$name = ucfirst($tab);
			echo "<li><span><a href=\"$url\" $class>$name</a></span></li>";
		}
	?>
	</ul>
</div>