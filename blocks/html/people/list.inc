<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Paginator $this->people
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Web\Url;
?>
<section>
    <header>
        <h1><?= $this->_(['person', 'people', count($this->people)]); ?></h1>
    </header>
	<table>
    <?php
        $helper = $this->template->getHelper('buttonLink');
        foreach ($this->people as $person) {
            $person_id = $person->getId();

            if (isset( $_REQUEST['return_url'])) {
                $callbackField       = isset($_REQUEST['callback_field']) ? $_REQUEST['callback_field'] : 'person_id';
                $url                 = new Url($_REQUEST['return_url']);
                $url->$callbackField = $person_id;

                $button = $helper->buttonLink($url, $this->_('person_choose'), 'choosePerson');
            }
            elseif (isset($_REQUEST['callback'])) {
                $this->template->addToAsset('scripts', BASE_URI.'/js/people/chooser-'.VERSION.'.js');

                $callback = preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback']);
                $url      = "javascript:self.opener.$callback('$person_id');";
                $button   = $helper->buttonLink($url, $this->_('person_choose'), 'choosePerson');
            }
            else {
                $url = parent::generateUri('people.view')."?person_id=$person_id";
            }

            $name  = self::escape($person->getFullname());
            echo "<tr><td><a href=\"$url\">$name</a></td>";
            if (Person::isAllowed('people', 'viewContactInfo')) {
                $email = self::escape($person->getEmail());
                $phone = self::escape($person->getPhone());
                echo "<td>$email</td><td>$phone</td>";
            }
            echo "</tr>";
        }
    ?>
	</table>
	<?php
        if (Person::isAllowed('people', 'update')) {
            // Preserve any extra parameters passed in
            $url    = new Url(parent::generateUrl('people.update'));
            $fields = ['firstname', 'lastname', 'email'];
            foreach ($_REQUEST as $key=>$value) {
                if (!in_array($key, $fields)) { $url->$key = $value; }
            }
            echo $helper->buttonLink($url, $this->_('person_add'), 'add');
        }
    ?>
</section>
