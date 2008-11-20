<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
?>
<div class="breadcrumbs">
	<ul>
		<li><a href="<?php echo BASE_UURL; ?>">Home</a></li>
		<li><a href="<?php echo BASE_URL; ?>/committees">Committees</a></li>
		<?php
			if (isset($this->committee))
			{
				$name = View::escape($this->committee->getName());
				echo "<li><a href=\"{$this->committee->getURL()}\">$name</a></li>";
			}
		?>
	</ul>
</div>
