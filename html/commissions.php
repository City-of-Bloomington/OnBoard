<?php
	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<div class="interfaceBox">
		<div class="titleBar">
			<?php $commission = new Commission($_GET['id']); 
						echo "{$commission->getName()}"; ?>
		</div>
		<table>
			<tr><th>Commission</th><th>Member Total</th>
			<?php
					echo "
					<tr>
					<td>{$commission->getName()}</td>
					<td>{$commission->getCount()}</td>
				</tr>";
		?>
		</table>
	</div>
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>