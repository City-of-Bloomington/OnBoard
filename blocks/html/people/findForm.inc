<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Blossom\Classes\View;

$fields = ['firstname', 'lastname', 'email'];
foreach ($fields as $f) {
	$$f = isset($_GET[$f]) ? View::escape($_GET[$f]) : '';
}
?>
<h2>Find a person</h2>
<form method="get" action="<?php echo BASE_URI; ?>/people">
	<fieldset>
		<table>
			<tr><td><label for="firstname"><?php echo $this->_('labels.firstname'); ?></label></td>
				<td><input name="firstname" id="firstname" value="<?php echo $firstname; ?>" /></td>
			</tr>

			<tr><td><label for="lastname"><?php echo $this->_('labels.lastname'); ?></label></td>
				<td><input name="lastname" id="lastname" value="<?php echo $lastname; ?>" /></td>
			</tr>

			<tr><td><label for="email"><?php echo $this->_(['labels.email','labels.emails',1]); ?></label></td>
				<td><input name="email" id="email" value="<?php echo $email; ?>" /></td>
			</tr>
		</table>

		<?php
			// Preserve any extra parameters passed in
			foreach ($_REQUEST as $key=>$value) {
				if (!in_array($key, $fields)) {
					$key   = View::escape($key);
					$value = View::escape($value);
					echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
				}
			}
		?>
		<button type="submit"><i class="fa fa-search"></i> Search</button>
	</fieldset>
</form>
