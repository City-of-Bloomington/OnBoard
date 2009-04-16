<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Tag extends ActiveRecord
{
	private $id;
	private $name;

	/**
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			$PDO = Database::getConnection();
			$query = $PDO->prepare('select * from tags where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('tags/unknownTag');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) {
					$this->$field = $value;
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->name) {
			throw new Exception('missingName');
		}
	}

	/**
	 * Saves this record back to the database
	 *
	 * This generates generic SQL that should work right away.
	 * You can replace this $fields code with your own custom SQL
	 * for each property of this class,
	 */
	public function save()
	{
		$this->validate();

		$fields = array();
		$fields['name'] = $this->name;

		// Split the fields up into a preparedFields array and a values array.
		// PDO->execute cannot take an associative array for values, so we have
		// to strip out the keys from $fields
		$preparedFields = array();
		foreach ($fields as $key=>$value) {
			$preparedFields[] = "$key=?";
			$values[] = $value;
		}
		$preparedFields = implode(",",$preparedFields);


		if ($this->id) {
			$this->update($values,$preparedFields);
		}
		else {
			$this->insert($values,$preparedFields);
		}
	}

	private function update($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "update tags set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert tags set $preparedFields";
		$query = $PDO->prepare($sql);
		$query->execute($values);
		$this->id = $PDO->lastInsertID();
	}

	public function delete()
	{
		if ($this->id) {
			$pdo = Database::getConnection();

			$query = $pdo->prepare('delete from topic_tags where tag_id=?');
			$query->execute(array($this->id));

			$query = $pdo->prepare('delete from tags where id=?');
			$query->execute(array($this->id));
		}
	}
	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

	/**
	 * @param string $string
	 */
	public function setName($string)
	{
		$this->name = trim($string);
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL.'/tags/viewTag.php?tag_id='.$this->id;
	}

	/**
	 * @return TopicList
	 */
	public function getTopics()
	{
		return new TopicList(array('tag_id'=>$this->id));
	}

	/**
	 * Returns the count of how many Topics use this tag
	 * If you pass in a topicList, the popularity will bedetermined
	 * for the given set of topics
	 *
	 * @param TopicList $topicList An optional list of topics to look in
	 * @return int
	 */
	public function getPopularity(TopicList $topicList=null)
	{
		if ($this->id) {
			$sql = "select count(*) as popularity from topic_tags where tag_id=:tag_id";
			$parameters = array(':tag_id'=>$this->id);
			if ($topicList) {
				$sql.= " and topic_id in ({$topicList->getSQL()})";
				$parameters = array_merge($parameters,$topicList->getParameters());
			}
			$sql.= " group by tag_id";

			$pdo = Database::getConnection();
			$query = $pdo->prepare($sql);
			$query->execute($parameters);
			$result = $query->fetchAll();
			return $result[0]['popularity'];
		}
	}
}
