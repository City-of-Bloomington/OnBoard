<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->departments
 * @param array $this->roles
 * @param array $this->authenticationMethods
 */
use Application\Models\Person;

$fields = ['firstname', 'lastname', 'username', 'email', 'department_id', 'role', 'authenticationMethod'];
foreach ($fields as $f) {
	$$f = isset($_GET[$f]) ? self::escape($_GET[$f]) : '';
}
?>
<section>
    <header>
        <h1><?= $this->_('find_person'); ?></h1>
    </header>
    <form method="get">
        <fieldset>
            <?php
                $h = $this->template->getHelper('field');

                foreach (['firstname', 'lastname', 'username', 'email'] as $f) {
                    echo $h->field([
                        'label' => $this->_($f),
                        'name'  => $f,
                        'id'    => $f,
                        'value' => $$f
                    ]);
                }

                $options = [['value'=>'']];
                foreach ($this->departments as $d) { $options[] = ['value'=>$d->getId(), 'label'=>$d->getName()]; }
				echo $h->field([
                    'name'     => 'department_id',
                    'id'       => 'department_id',
                    'label'    => $this->_('department'),
                    'value'    => $department_id,
                    'type'     => 'select',
                    'options'  => $options
				]);


				$options = [['value'=>'']];
				foreach (array_reverse($this->roles) as $r) { $options[] = ['value'=>$r]; }
				echo $h->field([
                    'name'     => 'role',
                    'id'       => 'role',
                    'label'    => $this->_('role'),
                    'value'    => $role,
                    'type'     => 'select',
                    'options'  => $options
				]);

                $options = [['value'=>'']];
                foreach ($this->authenticationMethods as $m) { $options[] = ['value'=>$m]; }
                echo $h->field([
                    'name'     => 'authenticationMethod',
                    'id'       => 'authenticationMethod',
                    'label'    => $this->_('authenticationMethod'),
                    'value'    => $authenticationMethod,
                    'type'     => 'select',
                    'options'  => $options
                ]);

                // Preserve any extra parameters passed in
                foreach ($_REQUEST as $key=>$value) {
                    if (!in_array($key, $fields)) {
                        $key   = self::escape($key);
                        $value = self::escape($value);
                        echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
                    }
                }
            ?>
            <button type="submit" class="search"><?= $this->_('search'); ?></button>
        </fieldset>
    </form>
</section>
