<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Blossom\Classes\View;

$fields = ['firstname', 'lastname', 'email'];
foreach ($fields as $f) {
	$$f = isset($_GET[$f]) ? View::escape($_GET[$f]) : '';
}
?>
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header">
        <h1><?php echo $this->_('find_person'); ?></h1>
    </header>
    <form method="get" action="<?php echo BASE_URI; ?>/people" class="fn1-uiBlock-content">
        <fieldset>
            <dl class="fn1-input-field">
                <dt><label for="firstname"><?php echo $this->_('firstname'); ?></label></dt>
                <dd><input name="firstname" id="firstname" value="<?php echo $firstname; ?>" /></dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label for="lastname"><?php echo $this->_('lastname'); ?></label></dt>
                <dd><input name="lastname" id="lastname" value="<?php echo $lastname; ?>" /></dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label for="email"><?php echo $this->_(['email','emails',1]); ?></label></dt>
                <dd><input name="email" id="email" value="<?php echo $email; ?>" /></dd>
            </dl>

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
            <button type="submit"><i class="fa fa-search"></i> <?php echo $this->_('search'); ?></button>
        </fieldset>
    </form>
</section>
