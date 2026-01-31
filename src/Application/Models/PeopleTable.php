<?php
/**
 * @copyright 2013-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class PeopleTable extends PdoRepository
{
    public static $columns = ['firstname', 'lastname', 'username', 'department_id'];

    public function __construct() { parent::__construct('people', __namespace__.'\Person'); }

    public function find(array $fields=[], ?string $order='p.lastname,p.firstname', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select distinct p.* from people p';
        $joins  = [];
        $where  = [];
        $params = [];

        foreach ($fields as $k=>$v) {
            switch ($k) {
                case 'user_account':
                    $where[] = $v ? 'p.username is not null' : 'p.username is null';
                break;

                case 'liaison':
                    if ($v) {
                        $joins[] = 'join committee_liaisons l on p.id=l.person_id';
                    }
                break;

                case 'committee_id':
                    $joins[] = 'join terms t on p.id=t.person_id';
                    $joins[] = 'join seats s on s.id=t.seat_id';
                    $where[] = 's.committee_id=:committee_id';
                    $params['committee_id'] = $v;
                break;

                case 'email':
                    $joins[] = 'join people_emails e on p.id=e.person_id';
                    $where[] = 'e.email=:email';
                    $params['email'] = $v;
                break;

                default:
                    if ($v && in_array($k, self::$columns)) {
                        $where[] = "p.$k=:$k";
                        $params[$k] = $v;
                    }
            }
        }

        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public function search(array $fields=[], ?string $order='p.lastname,p.firstname', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select p.* from people p';
        $joins  = [];
        $where  = [];
        $group  = null;
        $params = [];

        foreach ($fields as $k => $v) {
            switch ($k) {
                case 'user_account':
                    $where[] = $v ? 'p.username is not null' : 'p.username is null';
                break;

                case 'role':
                case 'department_id':
                    if ($v) {
                        $where[] = "p.$k=:$k";
                        $params[$k] = $v;
                    }
                break;

                case 'email':
                    $joins[] = 'join people_emails e on p.id=e.person_id';
                    $where[] = 'e.email like :email';
                    $params['email'] = "$v%";
                break;

                case 'involvement':
                    $select = "select p.*,
                                   (select count(*) from (
                                       select id from members    where person_id=p.id union
                                       select id from alternates where person_id=p.id union
                                       select id from liaisons   where person_id=p.id union
                                       select id from offices    where person_id=p.id
                                   ) i ) as involvement
                               from people p";
                    $group  = 'p.id ';
                    $group .= $v ? 'having involvement > 0' : 'having involvement = 0';
                break;

                default:
                    if ($v && in_array($k, self::$columns)) {
                        $where[] = "p.$k like :$k";
                        $params[$k] = "$v%";
                    }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, $group, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public function duplicatesByName(): array
    {
        $sql = "select firstname,lastname, count(*) as count
                from people
                group by firstname, lastname
                having count(*)>1";
        $qq  = $this->pdo->query($sql);
        return $qq->fetchAll(\PDO::FETCH_ASSOC);
    }
}
