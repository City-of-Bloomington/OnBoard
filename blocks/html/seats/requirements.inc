<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->seats An array of Seat objects
 */
use Michelf\MarkdownExtra;
?>
<section>
    <header>
        <h1><?= $this->_(['requirement', 'requirements', 2]); ?></h1>
    </header>

    <?php
        foreach ($this->seats as $seat) {
            $code = self::escape($seat->getCode());
            $requirements = $seat->getRequirements();

            $markdown = new MarkdownExtra();
            $requirements = $requirements
                ? $markdown->transform($requirements)
                : $this->_('none');

            echo "
            <dl class=\"fn1-output-field\">
                <dt>$code</dt>
                <dd>$requirements</td>
            </dl>
            ";
        }
    ?>
</section>