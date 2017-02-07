<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->statutes
 * @param Committee      $this->committee (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<section>
    <header>
        <h1><?= $this->_(['committeeStatute', 'committeeStatutes', count($this->statutes)]); ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if ($this->committee && Person::isAllowed('committeeStatutes', 'add')) {
                echo $helper->buttonLink(
                    BASE_URI.'/committeeStatutes/update?committee_id='.$this->committee->getId(),
                    $this->_('committeeStatute_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <table>
    <?php
        $userCanEdit = Person::isAllowed('committeeStatutes', 'edit');
        $editButton  = '';

        foreach ($this->statutes as $s) {
            $citation = self::escape($s->getCitation());
            $url      = self::escape($s->getUrl());
            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/committeeStatutes/update?committeeStatute_id='.$s->getId(),
                    $this->_('committeeStatute_edit'),
                    'edit',
                    ButtonLink::SIZE_ICON
                );
                $editButton = "<td>$editButton</td>";
            }
            echo "
            <tr><th><a href=\"$url\">$citation</a></th>
                <td><a href=\"$url\">$url</a></td>
                $editButton
            </tr>
            ";
        }
    ?>
    </table>
</section>
