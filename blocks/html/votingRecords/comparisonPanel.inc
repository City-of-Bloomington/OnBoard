<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->search The fields to be used for searching for content
 * @param array $this->people An array of Person objects
 * @param GET tag_id
 */
use Application\Models\Tag;
use Application\Models\TopicTable;
use Blossom\Classes\Block;
use Blossom\Classes\View;

// Show the Tag Cloud
// We're not currently choosing multiple tags, so we don't need to
// limit the tag list to only related tags.
// For now, we want to show a tag cloud based on the search
$tagCloud = new Block('tags/cloud.inc', ['search'=>$this->search]);
echo $tagCloud->render();

// If they ask for a tag, filter the topicList to only topics for that tag
if (isset($_GET['tag_id'])) {
	try {
		$tag = new Tag($_GET['tag_id']);
		$this->search['tag_id'] = $_GET['tag_id'];
	}
	catch (\Exception $e) {
		// Just ignore any bad tags
	}
}

// Show the voting comparison data
$title = isset($tag) ? View::escape(ucfirst($tag->getName())) : '';
echo "<div><h2>$title {$this->_('labels.votingRecordComparison')}</h2>";

$table = new TopicTable();
$this->topics = $table->find($this->search);
include APPLICATION_HOME.'/blocks/html/votingRecords/comparison.inc';

echo "</div>";
