<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Seat $this->seat
 */
use Blossom\Classes\Block;
use Michelf\MarkdownExtra;

$requirements   = self::escape($this->seat->getRequirements());
$markdown       = new MarkdownExtra();
$requirements   = $markdown->transform($requirements);

$name = self::escape($this->seat->getName());
$uri = BASE_URI.'/seats/view?seat_id='.$this->seat->getId();
?>
<section>
    <h1><a href="<?= $uri; ?>"><?= $name; ?></a></h1>
    <dl class="fn1-output-field">
        <dt><?= $this->_('appointed_by') ?></dt>
        <dd><?= $this->seat->getAppointer(); ?></dd>
    </dl>

    <dl class="fn1-output-field">
        <dt><?= $this->_(['requirement', 'requirements', 2]) ?></dt>
        <dd><?= $requirements; ?></dd>
    </dl>
</section>
