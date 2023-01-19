<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Sql\Select $users
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Web\Url;

$filename = APPLICATION_NAME.'-Users-'.date('Ymd');
header("Content-Disposition: attachment; filename=\"$filename.csv\"");

$out = fopen('php://output', 'w');

$columns = ['id', 'username', 'firstname','lastname', 'email', 'department', 'role'];
fputcsv($out, $columns);

foreach ($this->users as $user) {
	$row = [];
	foreach ($columns as $f) {
		$get     = 'get'.ucfirst($f);
		$row[$f] = $user->$get();
	}
	fputcsv($out, $row);
}
fclose($out);
