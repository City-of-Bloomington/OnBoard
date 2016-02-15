<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\ResultSet $this->people
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Url;
?>
<section     class="fn1-uiBlock">
    <header  class="fn1-uiBlock-header"><h1><?= $this->translate(['person','people',2]); ?></h1>
        <div class="fn1-uiBlock-headerTools">
        <?php
            $helper = $this->template->getHelper('buttonLink');

            if (Person::isAllowed('people', 'add')) {
                // Preserve any extra parameters passed in
                $url = new Url(BASE_URL.'/people/update');
                $fields = array('firstname','lastname','email');
                foreach ($_REQUEST as $key=>$value) {
                    if (!in_array($key, $fields)) { $url->$key = $value; }
                }

                echo $helper->buttonLink(
                    $url,
                    $this->translate('add_person'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
	<table class="fn1-table">
		<tbody>
		<?php
			foreach ($this->people as $person) {
				$button = '';
				if (Person::isAllowed('people', 'edit')) {
					$button = $helper->buttonLink(
						BASE_URI.'/people/update?person_id='.$person->getId(),
						$this->translate('edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}

				if (isset($_REQUEST['return_url'])) {
					$url = "$_REQUEST[return_url];person_id={$person->getId()}";
					$button = $helper->buttonLink($url, $this->_('person_choose'), 'choosePerson');
				}
				elseif (isset($_REQUEST['callback'])) {
					$this->template->addToAsset('scripts', BASE_URI.'/js/people/chooser.js');

					$callback = preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback']);
					$url = "javascript:self.opener.$callback('{$person->getId()}');";
					$button = $helper->buttonLink($url, $this->_('person_choose'), 'choosePerson');
				}

				$name  = self::escape($person->getFullname());
				$email = self::escape($person->getEmail());
				$phone = self::escape($person->getPhone());
				echo "
				<tr><td>$button</td>
					<td><a href=\"{$person->getUrl()}\">$name</a></td>
					<td>$email</td>
					<td>$phone</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</section>
