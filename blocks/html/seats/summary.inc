<?php
/**
 * @copyright 2016-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Seat $this->seat
 */
use Web\Block;
use Michelf\Markdown;

$name         = self::escape($this->seat->getName());
$uri          = parent::generateUri('seats.view').'?seat_id='.$this->seat->getId();
$requirements = self::escape($this->seat->getRequirements());

if ($requirements) {
    $requirements = Markdown::defaultTransform($requirements);
}
?>
<section>
    <header>
        <h1><a href="<?= $uri; ?>"><?= $name; ?></a></h1>
    </header>
    <dl>
        <dt><?= $this->_('appointed_by') ?></dt>
        <dd><?= $this->seat->getAppointer(); ?></dd>
    </dl>
    <dl>
        <dt><?= $this->_(['requirement', 'requirements', 2]) ?></dt>
        <dd><?= $requirements; ?></dd>
    </dl>
</section>
