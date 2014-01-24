<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee (optional)
 */
use Blossom\Classes\View;
?>
<div class="breadcrumbs">
	<ul>
		<li><a href="<?php echo BASE_URI; ?>"><?php echo $this->_('labels.home'); ?></a></li>
		<li><a href="<?php echo BASE_URI; ?>/committees">
				<?php echo $this->translate(['labels.committee','labels.committees',2]); ?>
			</a>
		</li>
		<?php
			if (isset($this->committee)) {
				$name = View::escape($this->committee->getName());
				echo "<li><a href=\"{$this->committee->getUri()}\">$name</a></li>";
			}
		?>
	</ul>
</div>
