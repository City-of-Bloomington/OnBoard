<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
use Application\Models\Person;
use Application\Models\RaceTable;

$fields = ['gender', 'race_id', 'address', 'city', 'state', 'zip'];
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	$$f  = self::escape($this->person->$get());
}

$title = $this->person->getId() ? $this->_('person_edit') : $this->_('person_add');
?>
<section class="updatePersonForm">
    <header>
        <h1><?= $title; ?></h1>
    </header>
	<form method="post" action="<?= BASE_URI; ?>/people/update">
		<fieldset><legend><?= $this->_('info_person'); ?></legend>
			<input name="person_id" type="hidden" value="<?= $this->person->getId(); ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if (!in_array($key, $fields)) {
						$key   = self::escape($key);
						$value = self::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}

				$this->_include('people/partials/personalInfoFields.inc');

                $field = $this->template->getHelper('field');
                echo $field->field([
                    'name'    => 'gender',
                    'id'      => 'gender',
                    'label'   => $this->_('gender'),
                    'value'   => $gender,
                    'type'    => 'radio',
                    'options' => [
                        ['value'=>'male',   'label'=>'M'],
                        ['value'=>'female', 'label'=>'F']
                    ]
                ]);

                $options = [['value'=>'']];
                $table = new RaceTable();
                $list  = $table->find();
                foreach ($list as $r) { $options[] = ['value'=>$r->getId(), 'label'=>parent::escape($r->getName())]; }
                echo $field->field([
                    'name'    => 'race_id',
                    'id'      => 'race_id',
                    'label'   => $this->_('race'),
                    'value'   => $race_id,
                    'type'    => 'select',
                    'options' => $options
                ]);
			?>
        </fieldset>

        <fieldset><legend><?= $this->_('address'); ?></legend>
        <?php
            echo $field->field([
                'name'  => 'address',
                'id'    => 'address',
                'label' => $this->_('address'),
                'value' => $address,
                'type'  => 'textarea'
            ]);

            $field->field(['name'=>'city', 'id'=>'city', 'label'=>$this->_('city'), 'value'=>$city]);

            $options = [['value'=>'']];
            foreach (Person::$STATES as $s) { $options[] = ['value'=>$s]; }
            echo $field->field([
                'name'    => 'state',
                'id'      => 'state',
                'label'   => $this->_('state'),
                'value'   => $state,
                'type'    => 'select',
                'options' => $options
            ]);

            echo $field->field(['name'=>'zip',  'id'=>'zip',  'label'=>$this->_('zip'),  'value'=>$zip ]);
        ?>
        </fieldset>

        <fieldset>
			<?php
				// Set the correct URL for the cancel button
				if (isset($_REQUEST['return_url'])) {
					$return_url = urlencode($_REQUEST['return_url']);
				}
				elseif ($this->person->getId()) {
					$return_url = $this->person->getUrl();
				}
				else {
					$return_url = BASE_URI.'/people';
				}

				$onclick = isset($_REQUEST['callback'])
					? 'onclick="self.close();"'
					: '';

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($return_url, $onclick);
			?>
		</fieldset>
	</form>
</section>
