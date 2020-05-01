<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Result $this->applications
 * @param Committee         $this->committee (optional)
 * @param Applicant         $this->applicant (optional)
 * @param boolean           $this->selectable (optional)
 */
use Application\Models\Person;
?>
<table class="fn1-table">
    <thead>
        <tr><?php
                if ($this->selectable) { echo "<th></th>"; }
                echo "<th>{$this->_('created')}</th>";
                if (!$this->applicant) { echo "<th>{$this->_('name')}</th>"; }
                if (!$this->committee) { echo "<th>{$this->_('committee')}</th>"; }
                echo $this->type === 'current'
                    ? "<th>{$this->_('expires')}</th><th></th>"
                    : "<th>{$this->_('archived')}</th>";
            ?>
    </thead>
    <tbody>
    <?php
        $userCanViewApplications = Person::isAllowed('committees',   'applications');
        $userCanViewApplicant    = Person::isAllowed('applicants',   'view');
        $userCanArchive          = Person::isAllowed('applications', 'archive');
        $userCanUnArchive        = Person::isAllowed('applications', 'unarchive');
        $userCanDelete           = Person::isAllowed('applications', 'delete');

        $helper = $this->template->getHelper('buttonLink');

        foreach ($this->applications as $a) {
            $application_id = $a->getId();
              $committee_id = $a->getCommittee_id();
              $applicant_id = $a->getApplicant_id();

            echo "<tr>";

            if ($this->selectable) {
                echo "<td><input name=\"applications[]\" type=\"checkbox\" value=\"$application_id\" /></td>";
            }

            $created = $a->getCreated(DATE_FORMAT);
            echo "<td>$created</td>";

            if (!$this->applicant) {
                $p    = $a->getApplicant();
                $name = self::escape("{$p->getFirstname()} {$p->getLastname()}");
                if ($userCanViewApplicant) {
                    $uri  = parent::generateUri('applicants.view')."?applicant_id=$applicant_id";
                    $name = "<a href=\"$uri\">$name</a>";
                }
                echo "<td>$name</td>";
            }

            if (!$this->committee) {
                $name = self::escape($a->getCommittee()->getName());
                if ($userCanViewApplications) {
                    $uri  = parent::generateUri('committees.applications')."?committee_id=$committee_id";
                    $name = "<a href=\"$uri\">$name</a>";
                }
                echo "<td>$name</td>";
            }


            if ($this->type === 'archived') {
                $archived = $a->getArchived(DATE_FORMAT);
                $button   = '';
                if ($userCanUnArchive) {
                    $button = $helper->buttonLink(
                        parent::generateUri('applications.unarchive')."?application_id=$application_id",
                        $this->_('application_unarchive')
                    );
                }
                echo "<td>$archived $button</td>";
            }
            else {
                $expires = $a->getExpires(DATE_FORMAT);
                echo "<td>$expires</td>";

                $archiveButton = '';
                $deleteButton  = '';
                if ($userCanArchive) {
                    $archiveButton = $helper->buttonLink(
                        parent::generateUri('applications.archive')."?application_id=$application_id",
                        $this->_('application_archive')
                    );
                }
                if ($userCanDelete) {
                    $deleteButton = $helper->buttonLink(
                        parent::generateUri('applications.delete')."?application_id=$application_id",
                        $this->_('application_delete'),
                        'delete'
                    );
                }
                echo "<td>$archiveButton $deleteButton</td>";
            }


            echo "</tr>";
        }
    ?>
    </tbody>
</table>
