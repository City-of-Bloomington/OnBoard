<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 * @param string $this->action
 */
?>
<div class="breadcrumbs">
	<ul>
		<li><a href="<?php echo BASE_URL; ?>">Home</a></li>
		<li><a href="<?php echo BASE_URL; ?>/committees">Committees</a></li>
		<?php
			$name = View::escape($this->seat->getCommittee()->getName());
			echo "<li><a href=\"{$this->seat->getCommittee()->getURL()}\">$name</a></li>";

			$name = View::escape($this->seat->getName());
			echo "<li><a href=\"{$this->seat->getURL()}\">$name</a></li>";
		?>
	</ul>
</div>
