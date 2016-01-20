<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
$fields = ['firstname', 'lastname', 'email'];
foreach ($fields as $f) {
	$$f = isset($_GET[$f]) ? self::escape($_GET[$f]) : '';
}
?>
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header">
        <h1><?= $this->_('find_person'); ?></h1>
    </header>
    <form method="get" action="<?= BASE_URI; ?>/people" class="fn1-uiBlock-content">
        <fieldset>
            <dl class="fn1-input-field">
                <dt><label  for="firstname"><?= $this->_('firstname'); ?></label></dt>
                <dd><input name="firstname" id="firstname" value="<?= $firstname; ?>" /></dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label  for="lastname"><?= $this->_('lastname'); ?></label></dt>
                <dd><input name="lastname" id="lastname" value="<?= $lastname; ?>" /></dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label  for="email"><?= $this->_(['email','emails',1]); ?></label></dt>
                <dd><input name="email" id="email" value="<?= $email; ?>" /></dd>
            </dl>

            <?php
                // Preserve any extra parameters passed in
                foreach ($_REQUEST as $key=>$value) {
                    if (!in_array($key, $fields)) {
                        $key   = self::escape($key);
                        $value = self::escape($value);
                        echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
                    }
                }
            ?>
            <button type="submit"><i class="fa fa-search"></i> <?= $this->_('search'); ?></button>
        </fieldset>
    </form>
</section>
