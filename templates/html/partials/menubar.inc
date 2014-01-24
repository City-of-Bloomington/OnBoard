<?php
use Application\Models\Person;
?>
<nav>
<?php
	$a = '<a href="%s">%s</a>';

	$routes = [
		'appointer'=>'appointers',
		'committee'=>'committees',
		'requirement'=>'requirements',
		'topic'=>'topics',
		'topicType'=>'topicTypes',
		'voteType'=>'voteTypes',
		'race'=>'races',
		'tag'=>'tags',
		'person'=>'people',
		'user'=>'users'
	];
	foreach ($routes as $singular=>$plural) {
		if (Person::isAllowed($plural, 'index')) {
			echo sprintf($a, BASE_URI.'/'.$plural, $this->_(["labels.$singular", "labels.$plural", 2]));
		}
	}
?>
</nav>
