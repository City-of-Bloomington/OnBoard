<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET sort
 * @param Zend\Db\Result $this->topics
 * @param Committee $this->committee
 */
use Blossom\Classes\Block;
use Blossom\Classes\Url;
use Application\Models\Person;
use Application\Models\Tag;
use Application\Models\TopicTable;

// All our queries on this panel will use the same search parameters
$search = [];
if (isset($this->committee)) { $search['committee_id'] = $this->committee->getId(); }


if (defined('VOTE_TRACKING') && VOTE_TRACKING
	&& isset($this->committee)
	&& Person::isAllowed('votes', 'edit')) {

	$invalidVotes = $this->committee->getInvalidVotes();
	if (count($invalidVotes)) {
		$block = new Block('votes/list.inc', ['votes'=>$invalidVotes, 'title'=>$this->_('labels.invalid_votes')]);
		echo $block->render();
	}
}


// If they ask for a tag, filter the topicList to only topics for that tag
$tags = [];
if (isset($_GET['tags'])) {
	$list = explode(',', $_GET['tags']);
	foreach ($list as $t) {
		try {
			$tag = new Tag($t);
			$tags[] = $tag->getId();
		}
		catch (\Exception $e) { }
	}
}
if (isset($_GET['tag_id'])) {
	try {
		$tag = new Tag($_GET['tag_id']);
		$tags[] = $tag->getId();
	}
	catch (\Exception $e) { }
}
if (count($tags)) {
	$search['tags'] = $tags;
}
$block = new Block('tags/cloud.inc', ['search'=>$search]);
echo $block->render('html', $this->template);

$addTopicButton = '';
if (isset($this->committee) && Person::isAllowed('topics', 'edit')) {
	$addTopicButton = sprintf(
		'<a class="btn" href="%s"><i class="fa fa-plus"></i> %s</a>',
		BASE_URI.'/topics/update?committee_id='.$this->committee->getId(),
		$this->translate('labels.add_topic')
	);
}
echo "
<div>
	<h2>{$this->_(['labels.topic','labels.topics',2])}
		$addTopicButton
	</h2>
	<ul class=\"tabBar\">
";
	$topicTable = new TopicTable();

	// We only want to display topics for a single year, chosen by the user
	// Lets create a new list, only including a single years worth of topics
	$years = $topicTable->getYears($search);
	$years = count($years) ? $years : [date('Y')];
	$displayYear = (isset($_GET['year']) && ctype_digit($_GET['year'])) ? $_GET['year'] : $years[0];
	$search['year'] = $displayYear;

	$url = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	foreach ($years as $year) {
		$class = $year == $displayYear ? 'class="current"' : '';
		$url->year = $year;
		echo "<li $class><a href=\"$url\">$year</a></li>";
	}
	echo "
	</ul>
	";

	// Display the topics
	$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
	$topics = $topicTable->find($search, null, true);
	$topics->setCurrentPageNumber($page);
	$topics->setItemCountPerPage(20);

	$block = new Block('topics/list.inc', ['topics'=>$topics]);
	echo $block->render('html', $this->template);

	$block = new Block('pageNavigation.inc', ['paginator'=>$topics]);
	echo $block->render('html', $this->template);
echo "
</div>
";
