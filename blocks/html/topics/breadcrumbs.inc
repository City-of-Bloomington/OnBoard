<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Tag $this->tag (optional)
 */
?>
<div class="breadcrumbs">
	<ul>
		<li><a href="<?php echo BASE_URL; ?>">Home</a></li>
		<li><a href="<?php echo BASE_URL; ?>/topics">Legislation</a></li>
		<?php
			if (isset($this->tag))
			{
				$name = View::escape($this->tag->getName());
				echo "<li>$name</li>";
			}
		?>
	</ul>
</div>
