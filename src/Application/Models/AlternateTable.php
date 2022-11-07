<?php
/**
 * @copyright 2022 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Select;

class AlternateTable extends TableGateway
{
    public function __construct() { parent::__construct('alternates', __namespace__.'\Alternate'); }

    public function find($fields=null, $order='startDate desc', $paginated=false, $limit=null)
    {
        $select = new Select('alternates');
        if ($fields) {
            foreach ($fields as $key=>$value) {
                switch ($key) {
                    case 'current':
                        if ($value) {
                            $select->where("startDate <= now()");
                            $select->where("(endDate is null or endDate >= now())");
                        }
                        else {
                            // current == false (the past)
                            $select->where("(endDate is not null and endDate <= now())");
                        }
                        break;

                    default:
                        $select->where([$key=>$value]);
                }
            }
        }
        return parent::performSelect($select, $order, $paginated, $limit);
    }

    //----------------------------------------------------------------
    // Route Action Functions
    //
    // These are functions that match the actions defined in the route
    //----------------------------------------------------------------
    public static function update(Alternate $alternate)
    {
        if ($alternate->getId()) {
            $action   = 'edit';
            $original = new Alternate($alternate->getId());
        }
        else {
            $action   = 'add';
            $original = [];
        }

        $alternate->save();

        CommitteeHistory::saveNewEntry([
            'committee_id'=> $alternate->getCommittee_id(),
            'tablename'   => 'alternates',
            'action'      => $action,
            'changes'     => [['original'=>$original, 'updated'=>$alternate->getData()]]
        ]);
    }

    public static function delete(Alternate $alternate)
    {
        $committee_id = $alternate->getCommittee_id();
        $changes      = [['original'=>$alternate->getData()]];
        $alternate->delete();

        CommitteeHistory::saveNewEntry([
            'committee_id' => $committee_id,
            'tablename'    => 'alternates',
            'action'       => 'delete',
            'changes'      => $changes
        ]);
    }
}
