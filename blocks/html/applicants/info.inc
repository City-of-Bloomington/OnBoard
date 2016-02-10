<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Applicant $this->applicant
 */
use Application\Models\Person;

$name = self::escape("{$this->applicant->getFirstname()} {$this->applicant->getLastname()}");
?>
<section>
    <header>
        <h1><?= $name; ?></h1>
        <div class="tools">
        <?php
            if (Person::isAllowed('applicants', 'edit')) {
                $helper = $this->template->getHelper('buttonLink');
                echo $helper->buttonLink(
                    BASE_URI.'/applicants/update?applicant_id='.$this->applicant->getId(),
                    $this->_('applicant_edit'),
                    'edit'
                );
            }
        ?>
        </div>
    </header>

    <table>
    <?php
        $email = self::escape($this->applicant->getEmail());
        $phone = self::escape($this->applicant->getPhone());
        echo "
        <tr><td>{$this->_('email')}</td><td>$email</td></tr>
        <tr><td>{$this->_('phone')}</td><td>$phone</td></tr>
        ";
    ?>
    </table>
</section>
