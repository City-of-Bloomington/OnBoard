<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Result $this->applicants
 */
use Application\Models\Person;
?>
<section>
    <header>
        <h1><?= $this->_(['applicant', 'applicants', count($this->applicants)]); ?></h1>
    </header>

    <table>
    <?php
        $fields = ['id', 'firstname', 'lastname', 'email', 'phone'];

        $userCanView   = Person::isAllowed('applicants', 'view');
        $userCanDelete = Person::isAllowed('applicants', 'delete');
        $helper = $this->template->getHelper('buttonLink');

        foreach ($this->applicants as $app) {
            foreach ($fields as $f) {
                $get = 'get'.ucfirst($f);
                $$f  = self::escape($app->$get());
            }

            $name = "$firstname $lastname";

            if ($userCanView) {
                $uri = parent::generateUri('applicants.view')."?applicant_id=$id";
                $name = "<a href=\"$uri\">$name</a>";
            }
            $deleteButton = '';
            if ($userCanDelete) {
                $deleteButton = $helper->buttonLink(
                    parent::generateUri('applicants.delete')."?applicant_id=$id",
                    $this->_('applicant_delete'),
                    'delete'
                );
            }

            echo "
            <tr><td>$name</td>
                <td>$email</td>
                <td>$phone</td>
                <td>$deleteButton</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
