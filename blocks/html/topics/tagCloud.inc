<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TopicList $this->topicList
 */
?>
<div class="interfaceBox tags">
	<h1>Tags</h1>
	<div id="tagWall">
	<?php
		$maxPopularity = 0;
		$tags = array();
		foreach ($this->topicList->getTags() as $tag) {
			$popularity = $tag->getPopularity($this->topicList);
			if ($popularity > $maxPopularity) {
				$maxPopularity = $popularity;
			}
			$tags[$tag->getId()]['name'] = View::escape($tag->getName());
			$tags[$tag->getId()]['popularity'] = $popularity;
		}

		// If the topicList is for a committee, use the Committee's URL
		// Otherwise, use the general Topics url
		$parameters = $this->topicList->getParameters();
		if (array_key_exists(':committee_id',$parameters)) {
			$committee = new Committee($parameters[':committee_id']);
			$url = $committee->getURL();
			$url->tab = 'topics';
		}
		else {
			$url = new URL(BASE_URL.'/topics');
		}


		foreach ($tags as $id=>$tag) {
			$percent = $tag['popularity'] / $maxPopularity * 100;

			if ($percent > 90) {
				$class = 'class="p90"';
			}
			elseif ($percent > 70) {
				$class = 'class="p70"';
			}
			elseif ($percent > 50) {
				$class = 'class="p50"';
			}
			elseif ($percent > 30) {
				$class = 'class="p30"';
			}
			elseif ($percent > 10) {
				$class = 'class="p10"';
			}
			else {
				$class = 'class="p0"';
			}

			$url->tag_id = $id;
			echo "
			<span $class>
				<a href=\"$url\">$tag[name]</a>
			</span>
			";
		}
	?>
	</div>
</div>
