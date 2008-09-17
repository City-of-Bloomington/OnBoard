<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->topic
 */
$type = View::escape($this->topic->getTopicType());
$number = View::escape($this->topic->getNumber());
$description = View::escape($this->topic->getDescription());
$synopsis = View::escape($this->topic->getSynopsis());
?>
<div class="interfaceBox">
	<h1><?php echo $type.$number; ?></h1>
	<h2><?php echo $description; ?></h2>
	<p><?php echo $synopsis; ?></p>
</div>
