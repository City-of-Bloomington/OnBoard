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
            if (Person::isAllowed('people')) {
                // Preserve any extra parameters passed in
                $url = new Url(BASE_URL.'/people/update');
                $fields = array('firstname','lastname','email');
                foreach ($_REQUEST as $key=>$value) {
                    if (!in_array($key, $fields)) { $url->$key = $value; }
                }

                $helper = $this->template->getHelper('buttonLink');
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
		<thead>
			<tr><th></th>
				<th><?= $this->_('username'); ?></th>
				<th><?= $this->_('name'); ?></th>
				<th><?= $this->_(['email','emails',1]); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->people as $person) {
				$editButton = '';
				if (Person::isAllowed('people')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/people/update?person_id='.$person->getId(),
						$this->translate('edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}

				if (isset($_REQUEST['return_url'])) {
					$url = "$_REQUEST[return_url];person_id={$person->getId()}";
				}
				elseif (isset($_REQUEST['callback'])) {
					$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
					$this->template->addToAsset('scripts', BASE_URI.'/js/people/chooser.js');

					$callback = preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback']);
					$url = "javascript:self.opener.$callback('{$person->getId()}');";
				}
				else {
					$url = $person->getUrl();
				}

				$name = self::escape($person->getFullname());
				echo "
				<tr><td>$editButton</td>
					<td>{$person->getUsername()}</td>
					<td><a href=\"$url\">$name</a></td>
					<td>{$person->getEmail()}</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</section>
