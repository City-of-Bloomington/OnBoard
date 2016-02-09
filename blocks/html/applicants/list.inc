<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->applicants
 */
use Application\Models\Person;
?>
<section>
    <h1><?= $this->_(['applicant', 'applicants', count($this->applicants)]); ?></h1>

    <table class="fn1-table">
    <?php
        $fields = ['firstname', 'lastname', 'email', 'phone'];

        foreach ($this->applicants as $app) {
            foreach ($fields as $f) {
                $get = 'get'.ucfirst($f);
                $$f  = self::escape($app->$get());
            }

            $name = "$firstname $lastname";

            echo "
            <tr><td>$name</td>
                <td>$email</td>
                <td>$phone</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>