<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->people
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
use Blossom\Classes\Url;
?>
<div>
	<h2><?php
			echo $this->translate(['labels.person','labels.people',2]);

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
					$this->translate('labels.add_person'),
					'add'
				);
			}
		?>
	</h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?php echo $this->_('labels.username'); ?></th>
				<th><?php echo $this->_('labels.name'); ?></th>
				<th><?php echo $this->_(['labels.email','labels.emails',1]); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->people as $person) {
				$editButton = '';
				if (Person::isAllowed('people')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/people/update?person_id='.$person->getId(),
						$this->translate('labels.edit'),
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

				$name = View::escape($person->getFullname());
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
</div>
