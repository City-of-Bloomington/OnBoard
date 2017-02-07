<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
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
            $name = self::escape($seat->getName());
            $requirements = $seat->getRequirements();

            $markdown = new MarkdownExtra();
            $requirements = $requirements
                ? $markdown->transform($requirements)
                : $this->_('none');

            echo "
            <dl>
                <dt>$code: $name</dt>
                <dd>$requirements</td>
            </dl>
            ";
        }
    ?>
</section>
