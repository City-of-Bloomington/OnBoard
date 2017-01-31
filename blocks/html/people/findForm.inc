<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;

$fields = ['firstname', 'lastname'];
if (Person::isAllowed('people', 'viewContactInfo')) {
    $fields[] = 'email';
}
foreach ($fields as $f) {
	$$f = isset($_GET[$f]) ? self::escape($_GET[$f]) : '';
}
?>
<section>
    <header>
        <h1><?= $this->_('find_person'); ?></h1>
    </header>
    <form method="get" action="<?= BASE_URI; ?>/people">
        <fieldset>
            <?php
                $h = $this->template->getHelper('field');
                foreach ($fields as $f) {
                    echo $h->field([
                        'label' => $this->_($f),
                        'name'  => $f,
                        'id'    => $f,
                        'value' => $$f
                    ]);
                }
                // Preserve any extra parameters passed in
                foreach ($_REQUEST as $key=>$value) {
                    if (!in_array($key, $fields)) {
                        $key   = self::escape($key);
                        $value = self::escape($value);
                        echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
                    }
                }
            ?>
            <button type="submit" class="search"><?= $this->_('search'); ?></button>
        </fieldset>
    </form>
</section>
