<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Topic $this->topic (optional)
 * @param Tag $this->tag (optional)
 */
use Blossom\Classes\View;
?>
<div class="breadcrumbs">
	<ul><li><a href="<?php echo BASE_URI; ?>">Home</a></li>
		<?php
			$topicsLabel = $this->_(['labels.topic', 'labels.topics', 1]);
			
			if (isset($this->topic)) {
				$committee = $this->topic->getCommittee();

				$c = View::escape($committee->getName());
				$url = $committee->getUri();
				$number = View::escape($this->topic->getNumber());
				echo "
				<li><a href=\"$url\">$c</a></li>
				<li><a href=\"$url;tab=topics\">$topicsLabel</a></li>
				<li><a href=\"{$this->topic->getUri()}\">$number</a></li>
				";

			}
			else {
				echo "<li><a href=\"".BASE_URI."/topics\">$topicsLabel</a></li>";
			}
			if (isset($this->tag)) {
				$name = View::escape($this->tag->getName());
				echo "<li>$name</li>";
			}
		?>
	</ul>
</div>
