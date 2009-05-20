<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Topic $this->topic (optional)
 * @param Tag $this->tag (optional)
 */
?>
<div class="breadcrumbs">
	<ul><li><a href="<?php echo BASE_URL; ?>">Home</a></li>
		<?php
			if (isset($this->topic)) {
				$committee = View::escape($this->topic->getCommittee()->getName());
				$url = $this->topic->getCommittee()->getURL();
				echo "<li><a href=\"$url\">$committee</a></li>";

				$url->tab = 'topics';
				echo "<li><a href=\"$url\">Legislation</a></li>";

				$name = View::escape($this->topic->getNumber());
				echo "<li><a href=\"{$this->topic->getURL()}\">$name</a></li>";

			}
			else {
				echo "<li><a href=\"".BASE_URL."/topics\">Legislation</a></li>";
			}
			if (isset($this->tag)) {
				$name = View::escape($this->tag->getName());
				echo "<li>$name</li>";
			}
		?>
	</ul>
</div>
