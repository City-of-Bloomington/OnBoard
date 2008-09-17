<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<h1>Add Member</h1>
<form method="get" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
<fieldset><legend>Find a User</legend>
	<input name="seat_id" type="hidden" value="<?php echo $this->seat->getId(); ?>" />
	<div style="width:30em; padding-bottom:2em; z-index:2">
		<input name="username" id="username" />
		<div id="autocomplete_container"></div>
		<script type="text/javascript">
		<?php
			$usernames = array();
			$list = new UserList();
			$list->find(null,'username');
			foreach($list as $u) { $usernames[] = "'{$u->getUsername()}'"; }
			$usernames = implode(',',$usernames);
			echo "var usernames = [$usernames];";
		?>
			var username_datasource = new YAHOO.widget.DS_JSArray(usernames);
			var autocomplete = new YAHOO.widget.AutoComplete("username","autocomplete_container", username_datasource);
		</script>
	</div>
	<button type="submit">Submit</button>
</fieldset>
</form>
