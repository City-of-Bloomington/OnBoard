<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Tag $this->tag
 */
?>
<div class="breadcrumbs">
	<ul>
		<li><a href="<?php echo BASE_URL; ?>">Home</a></li>
		<li><a href="<?php echo BASE_URL; ?>/tags">Tags</a></li>
		<?php
			if (isset($this->tag))
			{
				$name = View::escape($this->tag->getName());
				echo "
				<li><a href=\"{$this->tag->getURL()}\">$name</a></li>
				";
			}
		?>
	</ul>
</div>
