<?php
/**
 * @copyright 2007-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Sql\Select $users
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<section     class="fn1-uiBlock">
    <header  class="fn1-uiBlock-header"><h1><?= $this->_(['user','users',2]); ?></h1>
        <div class="fn1-uiBlock-headerTools">
        <?php
            if (Person::isAllowed('users')) {
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
	<table class="fn1-table">
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
			foreach ($this->users as $user) {
				$editButton = '';
				$deleteButton = '';
				if (Person::isAllowed('users')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/users/update?user_id='.$user->getId(),
						$this->translate('edit_account'),
						'edit',
						ButtonLink::SIZE_ICON
					);
					$deleteButton = $helper->buttonLink(
						BASE_URI.'/users/delete?user_id='.$user->getId(),
						$this->translate('delete_account'),
						'delete',
						ButtonLink::SIZE_ICON
					);
				}

				$name = self::escape($user->getFullname());
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