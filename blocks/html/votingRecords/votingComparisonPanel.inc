<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TopicList $this->topicList
 * @param array $this->people An array of Person objects
 * @param Committee $this->committee
 * @param GET tag_id
 */

// Show the Tag Cloud
// We're not currently choosing multiple tags, so we don't need to
// limit the tag list to only related tags.
// For now, we want to show a tag cloud based on the complete topicList
$url = $this->committee->getURL();
$url->tab = 'votes';
$tagCloud = new Block('tags/tagCloud.inc',
					  array('topicList'=>$this->topicList,
							'base_url'=>$url));
echo $tagCloud->render();



// If they ask for a tag, filter the topicList to only topics for that tag
if (isset($_GET['tag_id'])) {
	try {
		$tag = new Tag($_GET['tag_id']);
		$fields = $this->topicList->getFields();
		$fields['tag'] = $tag;
		$topicList = new TopicList($fields);
	}
	catch (Exception $e) {
		$topicList = $this->topicList;
	}
}
else {
	$topicList = $this->topicList;
}

// Show the voting comparison data
$title = isset($tag) ? View::escape(ucfirst($tag->getName())) : '';
echo "
<div class=\"interfaceBox votingComparison\">
	<h1>$title Voting Comparison</h1>
";
$votingComparison = new Block('votingRecords/votingRecordComparison.inc',
							  array('topicList'=>$topicList,
									'people'=>$this->people));
echo $votingComparison->render();

echo "
</div>
";
