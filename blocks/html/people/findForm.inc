<?php
/**
 * @copyright 2016-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->fields Fields to draw in the search form
 */
foreach ($this->fields as $f) {
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
                foreach ($this->fields as $f) {
                    echo $h->field([
                        'label' => $this->_($f),
                        'name'  => $f,
                        'id'    => $f,
                        'value' => $$f
                    ]);
                }
                // Preserve any extra parameters passed in
                foreach ($_REQUEST as $key=>$value) {
                    if (!in_array($key, $this->fields)) {
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
