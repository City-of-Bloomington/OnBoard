<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Member $this->member
 */
?>
<div class="interfaceBox">
	<h1>Vote Stats</h1>
	<table>
	<?php
		echo "<tr><th>Legislation</th>";
		$memberVoteValues = VotingRecord::getPossibleMemberVoteValues();
		foreach ($memberVoteValues as $value)
		{
			echo "<th>$value</th>";
		}

		$topicTypes = new TopicTypeList();
		$topicTypes->find();
		foreach ($topicTypes as $topicType)
		{
			$name = View::escape($topicType->getName());
			echo "<tr><th>$name</th>";
			$percentages = $member->getVotePercentages($topicType);

			foreach ($memberVoteValues as $memberVote)
			{
				echo "<td>$percentages[$memberVote]</td>";
			}
			echo "</tr>";
		}
		echo "</tr>"
	?>
	</table>
</div>

<div class="interfaceBox">
	<h1>Voting Comparison</h1>
	<table>
	<tr><th>Member</th><th>Percentage</th></tr>
	<?php
		$members = new MemberList(array('member_id'=>$this->member->getId()));
		foreach ($members as $otherMember) {
			$fullname = View::escape($otherMember->getFullname());
			$percent = $this->member->getAccordancePercentage($otherMember);
			echo "
			<tr><td><a href=\"{$otherMember->getURL()}\">
					$fullname</a>
				</td>
				<td>$percent</td></tr>
			";
		}
	?>
	</table>
</div>
