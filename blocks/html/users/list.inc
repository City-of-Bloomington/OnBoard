<?php
/**
 * @copyright 2007-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Sql\Select $users
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
			$helper        = $this->template->getHelper('buttonLink');

			if ($userCanEdit) {
                echo $helper->buttonLink(
                    parent::generateUri('users.update'),
                    $this->translate('create_account'),
                    'add'
                );
            }

            $current_url = new Url(Url::current_url(BASE_HOST));
			unset($current_url->page);
			$current_url->format = 'csv';
			echo $helper->buttonLink($current_url, $this->_('csv'), 'download');
        ?>
        </div>
    </header>
	<table>
		<thead>
			<tr><th></th>
				<th><?= $this->_('username'            ); ?></th>
				<th><?= $this->_('name'                ); ?></th>
				<th><?= $this->_('authenticationMethod'); ?></th>
				<th><?= $this->_('role'                ); ?></th>
				<th><?= $this->_('department'          ); ?>
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
				$class        = $user->getExternalIdentity() ? '' : ' class="missing"';
				if ($userCanEdit) {
					$editButton = $helper->buttonLink(
						parent::generateUri('users.update')."?user_id=$user_id;return_url=$return_url",
						$this->translate('edit_account'),
						'edit'
					);
                }
                if ($userCanDelete) {
					$deleteButton = $helper->buttonLink(
						parent::generateUri('users.delete')."?user_id=$user_id;return_url=$return_url",
						$this->translate('delete_account'),
						'delete'
					);
				}

				$name = self::escape($user->getFullname());
				if ($userCanView) {
                    $uri  = parent::generateUri('people.view')."?person_id=$user_id";
                    $name = "<a href=\"$uri\">$name</a>";
				}

				echo "
				<tr $class>
					<td>$editButton</td>
					<td>{$user->getUsername()}</td>
					<td>$name</td>
					<td>{$user->getAuthenticationMethod()}</td>
					<td>{$user->getRole()}</td>
					<td>{$user->getDepartment()}</td>
					<td>$deleteButton</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</section>
