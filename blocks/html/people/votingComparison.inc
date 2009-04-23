<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Person $this->person
 */
$firstname = View::escape($this->person->getFirstname());
?>
<div class="interfaceBox memberVoteComparison">
	<h1>Voting Comparison</h1>
	<p>Shows how often (as a percentage) that other members vote the same
		way as <?php echo $firstname; ?>
	</p>
	<div id="votingComparison" class="yui-skin-sam">
		<table id="votingComparisonTable">
			<thead>
				<tr><th>Member</th>
					<?php
						$finalVoteType = VoteType::getFinalVoteType();
						echo "<th>$finalVoteType Votes</th>";

						$topicTypes = new TopicTypeList();
						$topicTypes->find();
						foreach ($topicTypes as $topicType) {
							$name = View::escape($topicType);
							echo "<th>$name</th>";
						}
					?>
					<th>All Votes</th>
				</tr>
			</thead>
			<tbody>
			<?php
				// Gather all the data for this page
				$comparison = array();
				$peers = $this->person->getPeers();
				foreach ($peers as $peer) {
					$data = array('firstname'=>$peer->getFirstname(),
									'lastname'=>$peer->getLastname(),
									  'fullname'=>$peer->getFullname(),
									  'url'=>$peer->getURL());
					$data['allVotes'] = $this->person->getAccordancePercentage($peer);
					$data['finalVotes'] = $this->person->getAccordancePercentage($peer,null,$finalVoteType);
					foreach ($topicTypes as $topicType) {
						$data[$topicType->getName()] = $this->person->getAccordancePercentage($peer,$topicType->getTopics());
					}
					$comparison[] = $data;
				}

				foreach ($comparison as $row) {
					$fullname = View::escape($row['fullname']);
					echo "
					<tr><td><a href=\"$row[url]\">$fullname</a></td>
						<td>$row[finalVotes]</td>
					";
					foreach ($topicTypes as $topicType) {
						echo "<td>".$row[$topicType->getName()]."</td>";
					}
					echo "
						<td>$row[allVotes]</td>
					</tr>
					";
				}
			?>
			</tbody>
		</table>
	</div>
</div>
<?php
	$yuiLabels = array();
	$yuiLabels['final'] = $finalVoteType->getName().' Votes';
	foreach ($topicTypes as $topicType) {
		preg_match('/^\w*/',$topicType->getName(),$matches);
		$key = strtolower($matches[0]);
		$yuiLabels[$key] = $topicType->getName();
	}
	$yuiLabels['all'] = 'All Votes';
?>
<script type="text/javascript">
	YAHOO.util.Event.addListener(window,'load',function() {
		YAHOO.example.EnhanceFromMarkup = new function() {
			var myColumnDefs = [
				{key:'member',label:'Member',sortable:true},
				<?php
					$columns = array();
					foreach ($yuiLabels as $key=>$label) {
						$columns[] = "{key:'$key',label:'$label',formatter:YAHOO.util.Number.format({decimalPlaces:2}),sortable:true}";
					}
					echo implode(',',$columns);
				?>
			];

			this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get('votingComparisonTable'));
			this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
			this.myDataSource.responseSchema = {
				fields: [
					{key:'member'},
					<?php
						$columns = array();
						foreach ($yuiLabels as $key=>$label) {
							$columns[] = "{key:'$key',parser:'number'}";
						}
						echo implode(',',$columns);
					?>
				]
			};
			this.myDataTable = new YAHOO.widget.DataTable('votingComparison',
														  myColumnDefs,
														  this.myDataSource);
		}
	});
</script>
