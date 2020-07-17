<committees>
<?php
/**
 * @copyright 2020 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\ResultSet $this->data
 */
$fields = [
    'id','type','name','website', 'videoArchive',
    'email','phone','address','city','state','zip',
    'statutoryName','yearFormed', 'legislative',
    'seats', 'vacancies'
];
$data = [];
foreach ($this->data as $c) {
    echo "\t<committee id=\"$c[id]\">\n";
    foreach ($fields as $f) {
        if ($f != 'id') {
            echo "\t\t<$f>".parent::escape($c[$f])."</$f>\n";
        }
    }
    echo "\t</committee>\n";
}
?>
</committees>
