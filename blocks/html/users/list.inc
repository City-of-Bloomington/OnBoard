<?php
/**
 * @copyright 2007-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Zend\Db\Sql\Select $users
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Web\Url;
?>
<section>
    <header>
        <h1><?= $this->_(['user','users', 10]); ?></h1>
        <div>
        <?php
            $userCanView   = Person::isAllowed('people', 'view'  );
            $userCanEdit   = Person::isAllowed('users',  'update');
            $userCanDelete = Person::isAllowed('users',  'delete');
            if ($userCanEdit) {
                $helper = $this->template->getHelper('buttonLink');
                echo $helper->buttonLink(
                    BASE_URI.'/users/update',
                    $this->translate('create_account'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
	<table>
		<thead>
			<tr><th></th>
				<th><?= $this->_('username'); ?></th>
				<th><?= $this->_('name'); ?></th>
				<th><?= $this->_('authenticationMethod'); ?></th>
				<th><?= $this->_('role'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
            $return_url = new Url(Url::current_url(BASE_HOST));
            $return_url->purgeEmptyParameters();

			foreach ($this->users as $user) {
				$editButton   = '';
				$deleteButton = '';
				$user_id      = $user->getId();
				if ($userCanEdit) {
					$editButton = $helper->buttonLink(
						BASE_URI."/users/update?user_id=$user_id;return_url=$return_url",
						$this->translate('edit_account'),
						'edit'
					);
                }
                if ($userCanDelete) {
					$deleteButton = $helper->buttonLink(
						BASE_URI."/users/delete?user_id=$user_id;return_url=$return_url",
						$this->translate('delete_account'),
						'delete'
					);
				}

				$name = self::escape($user->getFullname());
				if ($userCanView) {
                    $uri  = BASE_URI."/people/view?person_id=$user_id";
                    $name = "<a href=\"$uri\">$name</a>";
				}
				echo "
				<tr><td>$editButton</td>
					<td>{$user->getUsername()}</td>
					<td>$name</td>
					<td>{$user->getAuthenticationMethod()}</td>
					<td>{$user->getRole()}</td>
					<td>$deleteButton</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</section>
