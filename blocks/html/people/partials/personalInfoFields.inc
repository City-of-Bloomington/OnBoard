<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $firstname
 * @param string $lastname
 * @param string $email
 * @param string $phone
 */
?>
<dl class="fn1-input-field">
    <dt><label  for="firstname" class="required"><?= $this->_('firstname'); ?></label></dt>
    <dd><input name="firstname" id="firstname" value="<?= $firstname; ?>" /></dd>
</dl>

<dl class="fn1-input-field">
    <dt><label  for="lastname" class="required"><?= $this->_('lastname'); ?></label></dt>
    <dd><input name="lastname" id="lastname" value="<?= $lastname; ?>" /></dd>
</dl>

<dl class="fn1-input-field">
    <dt><label  for="email"><?= $this->_('email'); ?></label></dt>
    <dd><input name="email" id="email" value="<?= $email; ?>" /></dd>
</dl>

<dl class="fn1-input-field">
    <dt><label  for="phone"><?= $this->_('phone'); ?></label></dt>
    <dd><input name="phone" id="phone" type="tel" value="<?= $phone; ?>" /></dd>
</dl>
