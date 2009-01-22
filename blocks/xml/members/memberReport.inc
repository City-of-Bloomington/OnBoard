<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Member $this->member
 */
?>
<votingRecord>
	<votePercentagesByTopicType>
	<?php
		$memberVoteValues = VotingRecord::getPossibleMemberVoteValues();

		$topicTypes = new TopicTypeList();
		$topicTypes->find();
		foreach ($topicTypes as $topicType)
		{
			$name = View::escape($topicType->getName());
			echo "
			<topicType id=\"{$topicType->getId()} name=\"$name\">
			";

			$percentages = $member->getVotePercentages($topicType);
			foreach ($memberVoteValues as $memberVote)
			{
				echo "
					<$memberVote>$percentages[$memberVote]</$memberVote>
				";
			}
			echo "
			</topicType>
			";
		}
	?>
	</votePercentagesByTopicType>

	<votingAccordance>
	<?php
		$members = new MemberList(array('member_id'=>$this->member->getId()));
		foreach ($members as $otherMember)
		{
			$fullname = View::escape($otherMember->getFullname());
			$percent = $this->member->getAccordancePercentage($otherMember);
			echo "
			<member id=\"{$otherMember->getId()} name=\"$fullname\">
				<percentage>$percent</percentage>
			</member>
			";
		}
	?>
	</votingAccordance>
</votingRecord>
