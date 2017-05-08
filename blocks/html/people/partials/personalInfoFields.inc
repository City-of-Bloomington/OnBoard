<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
$fields = ['firstname', 'lastname', 'email', 'phone'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = self::escape($this->person->$get());
}
$field = $this->template->getHelper('field');
echo $field->field(['name'=>'firstname', 'id'=>'firstname', 'label'=>$this->_('firstname'), 'value'=>$firstname, 'required'=>true]);
echo $field->field(['name'=>'lastname',  'id'=>'lastname',  'label'=>$this->_('lastname'),  'value'=>$lastname,  'required'=>true]);
echo $field->field(['name'=>'email',     'id'=>'email',     'label'=>$this->_('email'),     'value'=>$email,     'type'=>'email']);
echo $field->field(['name'=>'phone',     'id'=>'phone',     'label'=>$this->_('phone'),     'value'=>$phone,     'type'=>'tel'  ]);
