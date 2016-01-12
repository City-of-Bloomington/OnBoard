<?php
use Application\Models\Person;
?>
<div class="fn1-nav1">
  <nav class="fn1-nav1-container">
  <?php
    $a = '<a href="%s">%s</a>';

    $routes = [
      'committee'=>'committees',
    ];
    if (defined('VOTE_TRACKING') && VOTE_TRACKING) {
      $routes['voteType']   = 'voteTypes';
      $routes['topic']      = 'topics';
      $routes['topicTypes'] = 'topicTypes';
    }
    foreach ($routes as $singular=>$plural) {
      if (Person::isAllowed($plural, 'index')) {
        echo sprintf($a, BASE_URI.'/'.$plural, $this->_(["$singular", "$plural", 2]));
      }
    }
  ?>
  </nav>
</div>
