<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->search
 */
use Application\Models\TagTable;
use Blossom\Classes\Url;
use Blossom\Classes\View;
?>
<div class="tags">
<?php
	$table = new TagTable();

	$maxPopularity = 0;
	$tags = array();
	foreach ($table->find($this->search) as $tag) {
		$popularity = $tag->getPopularity($this->topicList);
		if ($popularity > $maxPopularity) {
			$maxPopularity = $popularity;
		}
		$tags[$tag->getId()]['name'] = View::escape($tag->getName());
		$tags[$tag->getId()]['popularity'] = $popularity;
	}

	$url = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	foreach ($tags as $id=>$tag) {
		$percent = $tag['popularity'] / $maxPopularity * 100;

		if     ($percent > 90) { $class = 'class="p90"'; }
		elseif ($percent > 70) { $class = 'class="p70"'; }
		elseif ($percent > 50) { $class = 'class="p50"'; }
		elseif ($percent > 30) { $class = 'class="p30"'; }
		elseif ($percent > 10) { $class = 'class="p10"'; }
		else                   { $class = 'class="p0"' ;  }

		$url->tag_id = $id;
		$current = '';
		if (isset($_GET['tag_id']) && $_GET['tag_id']==$id) {
			$current = 'class="current"';
		}
		echo "
		<span $class>
			<a href=\"$url\" $current>$tag[name]</a>
		</span>
		";
	}
?>
</div>
