<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->seats An array of Seat objects
 */
use Michelf\MarkdownExtra;
?>
<section>
    <h1><?= $this->_('requirements'); ?></h1>
    <table>
    <?php
        foreach ($this->seats as $seat) {
            $code = self::escape($seat->getCode());

            $markdown = new MarkdownExtra();
            $requirements = $markdown->transform($seat->getRequirements());

            echo "
            <tr><th>$code</th>
                <td>$requirements</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>