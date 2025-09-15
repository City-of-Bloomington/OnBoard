<?php
/**
 * @copyright 2013-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Predicate\Like;

class PeopleTable extends TableGateway
{
    public static $columns = ['firstname', 'lastname', 'username', 'department_id'];

    public function __construct() { parent::__construct('people', __namespace__.'\Person'); }

    public function find($fields=null, $order='lastname', $paginated=false, $limit=null)
    {
        $select = new Select(['p'=>'people']);
        $select->quantifier(Select::QUANTIFIER_DISTINCT);

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'user_account':
                        if ($v) { $select->where('p.username is not null'); }
                        else    { $select->where('p.username is null'    ); }
                    break;

                    case 'liaison':
                        if ($v) {
                            $select->join(['l'=>'committee_liaisons'], 'p.id=l.person_id', []);
                        }
                    break;

                    case 'committee_id':
                        $select->join(['t'=>'terms'], 'p.id=t.person_id', []);
                        $select->join(['s'=>'seats'], 't.seat_id=s.id',   []);
                        $select->where(['s.committee_id'=>$v]);
                    break;

                    case 'email':
                        $select->join(['e'=>'people_emails'], 'p.id=e.person_id', []);
                        $select->where(['e.email'=>$v]);
                    break;

                    default:
                        if ($v && in_array($k, self::$columns)) {
                            $select->where(["p.$k"=>$v]);
                        }
                }
            }
        }

        $order = 'p.'.$order;
        return parent::performSelect($select, $order, $paginated, $limit);
    }

    public function search($fields, $order='lastname', $paginated=false, $limit=null)
    {
        $select = new Select(['p'=>'people']);

        foreach ($fields as $k => $v) {
            switch ($k) {
                case 'user_account':
                    if ($v) { $select->where('p.username is not null'); }
                    else    { $select->where('p.username is null'    ); }
                break;

                case 'role':
                case 'department_id':
                    if ($v) { $select->where(["p.$k"=>$v]); }
                break;

                case 'email':
                    $select->join(['e'=>'people_emails'], 'p.id=e.person_id', []);
                    $select->where->like('e.email', "$v%");
                break;

                case 'involvement':
                    $sql = "(select count(*) from (
                                select id from members where person_id=p.id
                                union
                                select id from alternates where person_id=p.id
                                union
                                select id from liaisons where person_id=p.id
                                union
                                select id from offices where person_id=p.id
                            ) i )";
                    $select->columns(['*', 'involvement'=> new Expression($sql)], false);

                    if ($v) { $select->having('involvement > 0'); }
                    else    { $select->having('involvement = 0'); }
                break;

                default:
                    if ($v && in_array($k, self::$columns)) {
                        $select->where->like("p.$k", "$v%");
                    }
            }
        }
        $order = 'p.'.$order;
        return parent::performSelect($select, $order, $paginated, $limit);
    }

    public static function duplicatesByName()
    {
        $sql = "select firstname,lastname, count(*) as count
                from people
                group by firstname, lastname
                having count(*)>1";
        $db     = Database::getConnection();
        return $db->query($sql)->execute();
    }
}
