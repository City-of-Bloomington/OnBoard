<nav>
	<h2><?php echo $this->translate('admin'); ?></h2>
	<ul>
	<?php
		$li = '<li><a href="%s">%s</a></li>';
		echo sprintf($li, BASE_URI.'/people', $this->translate(['person','people',2]));
		echo sprintf($li, BASE_URI.'/users' , $this->translate(['user'  ,'users' ,2]));
	?>
	</ul>
</nav>
