<?php
use Blossom\Classes\View;
?>
<header>
	<ul id="utilityBar">
		<?php
			$li = '<li><a href="%s">%s</a></li>';

			if (isset($_SESSION['USER'])) {
				echo sprintf($li, BASE_URI.'/login/logout', $this->translate('labels.logout'));

				$name = View::escape($_SESSION['USER']->getFullname());
				echo sprintf('<li>'.sprintf($this->translate('messages.signed_in_as'), $name).'</li>');
			}
			else {
				$return_url = View::escape($_SERVER['REQUEST_URI']);
				echo sprintf($li, BASE_URI.'/login?return_url='.$return_url, $this->translate('labels.login'));
			}
		?>
	</ul>
	<h1><a href="<?php echo BASE_URL; ?>/"><?php echo APPLICATION_NAME; ?></a></h1>
	<div id="location_name">
		<a href="<?php echo BASE_URL; ?>">City of Bloomington, Mark Kruzan, Mayor</a>
	</div>
	<?php
		include __DIR__.'/menubar.inc';
	?>
</header>
