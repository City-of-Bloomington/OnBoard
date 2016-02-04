<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->applications
 */
use Application\Models\Person;
?>
<section>
    <h1><?= $this->_(['application', 'applications', count($this->applications)]); ?></h1>

    <table class="fn1-table">
    <?php
        $fields = ['firstname', 'lastname', 'email', 'phone'];

        foreach ($this->applications as $app) {
            foreach ($fields as $f) {
                $get = 'get'.ucfirst($f);
                $$f  = self::escape($app->$get());
            }

            $name = "$firstname $lastname";

            if (Person::isAllowed('applications', 'view')) {
                $uri = BASE_URI.'/applications/view?application_id='.$app->getId();
                $name = "<a href=\"$uri\">$name</a>";
            }
            $created  = $app->getCreated (DATETIME_FORMAT);
            echo "
            <tr><td>$name</td>
                <td>$email</td>
                <td>$phone</td>
                <td>$created</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>