<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET sort
 * @param TopicList $this->topicList
 * @param Committee $this->committee
 */

// Show the Tag Cloud
// We're not currently choosing multiple tags, so we don't need to
// limit the tag list to only related tags.
// For now, we want to show a tag cloud based on the complete topicList
$url = $this->committee->getURL();
$url->tab = 'topics';
$tagCloud = new Block('tags/tagCloud.inc',
					  array('topicList'=>$this->topicList,
							'base_url'=>$url));
echo $tagCloud->render();

if (userHasRole(array('Administrator','Clerk'))) {
	$invalidVotes = $this->committee->getInvalidVotes();
	if (count($invalidVotes)) {
		$invalidVotesBlock = new Block('votes/voteList.inc',
										array('voteList'=>$invalidVotes,
										'title'=>'Invalid Votes'));
		echo $invalidVotesBlock->render();
	}
}


// We only want to display topics for a single year, chosen by the user
// Lets create a new list, only including a single years worth of topics
$years = $this->topicList->getYears();
$years = count($years) ? $years : array(date('Y'));
$displayYear = (isset($_GET['year']) && ctype_digit($_GET['year']))
			? $_GET['year']
			: $years[0];
$search = $this->topicList->getFields();
$search['year'] = $displayYear;
// If they ask for a tag, filter the topicList to only topics for that tag
if (isset($_GET['tag_id'])) {
	try {
		$tag = new Tag($_GET['tag_id']);
		$search['tag_id'] = $tag->getId();
	}
	catch (Exception $e) {
	}
}
$topics = new TopicList();
$topics->find($search,$this->topicList->getSort());




// Display the years list and the sorting buttons
$userCanEdit = userHasRole(array('Administrator','Clerk'));
if (count($topics) || $userCanEdit) {
	$title = isset($tag) ? View::escape(ucfirst($tag->getName())) : '';

	$addTopicButton = '';
	if ($userCanEdit) {
		$url = new URL(BASE_URL.'/topics/addTopic.php');
		$url->committee_id = $this->committee->getId();
		$addTopicButton = "<a class=\"add button\" href=\"$url\">Add</a>";
	}
	echo "
	<div class=\"interfaceBox legislation\">
		<h1>$addTopicButton $title Legislation</h1>
		<div class=\"years\">
	";
		$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		arsort($years);
		foreach ($years as $year) {
			$url->year = $year;
			$class = $year==$displayYear ? 'class="current"' : '';
			echo "<a href=\"$url\" $class>$year</a> ";
		}
	echo "</div>";

	$numberSortURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	if (preg_match('/number desc/',$topics->getSort())) {
		$numberSortURL->sort = 'number';
		$numberDirection = 'up';
	}
	else {
		$numberSortURL->sort = 'number desc';
		$numberDirection = 'down';
	}

	$dateSortURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	if (preg_match('/date desc/',$topics->getSort())) {
		$dateSortURL->sort = 'date';
		$dateDirection = 'up';
	}
	else {
		$dateSortURL->sort = 'date desc';
		$dateDirection = 'down';
	}
	echo "
		<div>
			<label>Sort:</label>
			<a href=\"$numberSortURL\" class=\"$numberDirection\">Number</a>
			<a href=\"$dateSortURL\" class=\"$dateDirection\">Date</a>
		</div>
	";


	// Paginate the topics if there's alot of them
	if (count($topics) > 15) {
		$pages = new Paginator($topics,15);
		$page = (isset($_GET['page']) && $_GET['page'])
				? (int)$_GET['page']
				: 0;
		if (!$pages->offsetExists($page)) {
			$page = 0;
		}
		$topicList = new LimitIterator($topics,$pages[$page],$pages->getPageSize());
	}
	else {
		$topicList = $topics;
	}


	// Display the topics
	$topicBlock = new Block('topics/topicList.inc',array('topicList'=>$topicList));
	echo $topicBlock->render();


	// Display the page navigation links
	if (isset($pages)) {
		$pageNavigation = new Block('pageNavigation.inc');
		$pageNavigation->page = $page;
		$pageNavigation->pages = $pages;
		$pageNavigation->url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

		echo $pageNavigation->render();
	}
}
