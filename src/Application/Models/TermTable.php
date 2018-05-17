<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class TermTable extends TableGateway
{
	public function __construct() { parent::__construct('terms', __namespace__.'\Term'); }

	public function find($fields=null, $order='startDate desc', $paginated=false, $limit=null)
	{
		$select = new Select('terms');
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
						$date = date(ActiveRecord::MYSQL_DATE_FORMAT, $value);
						$select->where("(startDate is null or startDate<='$date')");
						$select->where("(endDate   is null or endDate  >='$date')");
						break;

					case 'before':
						$date = date(ActiveRecord::MYSQL_DATE_FORMAT, $value);
						$select->where("startDate is null or startDate < '$date'");
						$select->where("endDate < '$date'");
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
	public static function update(Term $term, array $post)
	{
        $action = $term->getId() ? 'edit' : 'add';
        $change = $action == 'edit' ? [CommitteeHistory::STATE_ORIGINAL=>$term->getData()] : [];
        $term->handleUpdate($post);
        $term->save();
        $change[CommitteeHistory::STATE_UPDATED] = $term->getData();

        CommitteeHistory::saveNewEntry([
            'committee_id' => $term->getSeat()->getCommittee_id(),
            'tablename'    => 'terms',
            'action'       => $action,
            'changes'      => [$change]
        ]);
	}

	public static function delete(Term $term)
	{
        $seat   = $term->getSeat();
        $change = [CommitteeHistory::STATE_ORIGINAL=>$term];
        $term->delete();

        CommitteeHistory::saveNewEntry([
            'committee_id' => $seat->getCommittee_id(),
            'tablename'    => 'terms',
            'action'       => 'delete',
            'changes'      => [$change]
        ]);
	}
}
