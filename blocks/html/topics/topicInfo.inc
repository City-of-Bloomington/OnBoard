<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */

$name = $this->topic;
$description = View::escape($this->topic->getDescription());
$synopsis = View::escape($this->topic->getSynopsis();
$committe = $this->topic->getCommittee();
$committeeName = View::escape($committee->getCommitteeName();
echo "
<h1>$committeeName</h1>
<h2>$name</h2>
<p><b>$description</b></p>
<p>$synopsis</p>

";
