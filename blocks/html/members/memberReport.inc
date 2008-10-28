<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param MemberList $this->memberList
 * @param Seat $this->seat (optional)
 */
$member = $this->member;
$topicTypeList = new TopicTypeList();
$topicTypeList->find();

?>
<div class="interfaceBox">

	<h1> Voting Percentages </h1>
	<table>
	<?php
		$done = false;
		$str = "<tr><th>Legislation</th>";
		foreach($topicTypeList as $topicType){
			$types = $member->findVoteByType($topicType->getId());

			$str2 = "<tr><td>".$topicType->getName()."</td>";
			foreach($types as $name=>$value)
			{
				if(!$done){
					$str.="<th>".ucfirst($name)."</th>";
				}
				$str2.="<td>".$value."</td>";
			}
			if(!$done){
				$str.="</tr>";
				echo $str;
			}
			$done = true;
			$str2.="</tr>";
			echo $str2;
		}
	?>
	</table>
</div>

<div class="interfaceBox">
	<h1> Voting Accords With Other Members </h1>
	<table>
	<th>Member</th><th>Percentage</th></tr>
	<?php
	$members = new MemberList();
	$members->findMemberVoteList($member->getId());
	foreach($members as $otherMember){
		$percent = $member->findAccordPerecent($otherMember->getId());
		echo "<tr><td>".$otherMember->getUser()->getFullname()."</td>";
		echo "<td>".$percent."</td></tr>";	
	}
	?>
	</table>

</div>
