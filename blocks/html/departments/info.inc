<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Department $this->department
 */
declare (strict_types=1);

use Application\Models\Person;

$committees = $this->department->getCommittees();
?>
<h1><a href="<?= parent::generateUri('departments.index'); ?>">
        <?= $this->_(['department', 'departments', 10]); ?>
    </a>
</h1>
<h2><?= $this->department->getName(); ?></h2>

<section>
    <header><h1><?= $this->_(['committee', 'committees', 10]); ?></h1></header>
    <table>
    <?php
        foreach ($committees as $c) {
            $url  = parent::generateUri('committees.info')."?committee_id=".$c->getId();
            echo "<tr><td><a href=\"$url\">{$c->getName()}</a></td></tr>";
        }
    ?>
    </table>
</section>

<?php
    if (Person::isAllowed('users', 'view')) {
        $this->users = $this->department->getUsers();
        $this->_include('users/list.inc');
    }
?>
