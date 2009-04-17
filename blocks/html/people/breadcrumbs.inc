<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
?>
<div class="breadcrumbs">
	<ul>
		<li><a href="<?php echo BASE_URL; ?>">Home</a></li>
		<li><a href="<?php echo BASE_URL; ?>/people">People</a></li>
		<?php
			if (isset($this->person)) {
				$name = View::escape($this->person->getFullname());
				echo "<li><a href=\"{$this->person->getURL()}\">$name</a></li>";
			}
		?>
	</ul>
</div>
