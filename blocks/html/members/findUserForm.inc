<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<h1>Add Member</h1>
<form method="get" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam" id="autocomplete_form">
<fieldset><legend>Find an Exiting User</legend>
	<input name="seat_id" type="hidden" value="<?php echo $this->seat->getId(); ?>" />
	<input type="hidden" name="user_id" id="user_id" />
	<div style="width:30em; padding-bottom:2em; z-index:2">
		<input name="search" id="search" />
		<div id="autocomplete_container"></div>
	</div>
	<script type="text/javascript">
	<?php
		$names = array();

		$list = new UserList();
		$list->find(null,'lastname,firstname');
		foreach($list as $u)
		{
			$id = $u->getId();
			$fullname = addslashes($u->getFullname());

			$names[] = "{name:'$fullname',id:$id}";
		}
		$names = implode(",\n\t\t",$names);
		echo "
		var names = [$names];
		";
	?>
		// Use a LocalDataSource
		var namesData = new YAHOO.util.LocalDataSource(names);
		namesData.responseSchema = {fields : ["name", "id"]};

		// Instantiate the AutoComplete
		var autocomplete = new YAHOO.widget.AutoComplete("search", "autocomplete_container", namesData);
		autocomplete.queryMatchCase = false;
		autocomplete.queryMatchContains = true;
		autocomplete.resultTypeList = false;

		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var selectHandler = function(sType, aArgs) {
			document.getElementById('user_id').value = aArgs[2].id;
		};
		autocomplete.itemSelectEvent.subscribe(selectHandler);
	</script>
	<button type="submit">Submit</button>
</fieldset>
</form>
