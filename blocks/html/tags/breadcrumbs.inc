<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Tag $this->tag
 */
use Blossom\Classes\View;
?>
<div class="breadcrumbs">
	<ul><li><a href="<?php echo BASE_URI; ?>">Home</a></li>
		<li><a href="<?php echo BASE_URI; ?>/tags">Tags</a></li>
		<?php
			if (isset($this->tag)) {
				$name = View::escape($this->tag->getName());
				echo "<li><a href=\"{$this->tag->getUri()}\">$name</a></li>";
			}
		?>
	</ul>
</div>
