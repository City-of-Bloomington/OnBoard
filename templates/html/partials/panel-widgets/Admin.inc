<nav>
	<h2><?php echo $this->translate('labels.admin'); ?></h2>
	<ul>
	<?php
		$li = '<li><a href="%s">%s</a></li>';
		echo sprintf($li, BASE_URI.'/people', $this->translate(['labels.person','labels.people',2]));
		echo sprintf($li, BASE_URI.'/users' , $this->translate(['labels.user'  ,'labels.users' ,2]));
	?>
	</ul>
</nav>
