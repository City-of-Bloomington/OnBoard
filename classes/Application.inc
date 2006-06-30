<?php
	class Application
	{
		private $id;
		private $committee_id;
		private $resumePath;
		private $firstname;
		private $lastname;
		private $email;
		private $address;
		private $city;
		private $zipcode;
		private $homePhone;
		private $workPhone;
		private $resident;
		private $occupation;
		private $interest;
		private $qualifications;
		private $timestamp;

		public function __construct($id=null)
		{
			global $PDO;

			if ($id)
			{
				$sql = "select * from applications where id=$id";
				$result = $PDO->query($sql);
				if ($result)
				{
					if ($row = $result->fetch())
					{
						# This will load all fields in the table as properties of this class.
						# You may want to replace this with, or add your own extra, custom loading
						foreach($row as $field=>$value) { if ($value) $this->$field = $value; }


						$result->closeCursor();
					}
					else { throw new Exception($sql); }
				}
				else { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
			}
			else
			{
				# This is where the code goes to generate a new, empty instance.
				# Set any default values for properties that need it here
			}
		}
		

		public function save()
		{
			# Check for required fields here.  Throw an exception if anything is missing.
			if (!$this->email || !$this->firstname || !$this->lastname || !$this->interest || !$this->qualifications || !$this->committee_id) 
			{ 
				throw new Exception("missingRequiredFields"); 
			}

			# This generates generic SQL that should work right away.
			# You can (and maybe should) replace this $fields code with your own custom SQL
			# for each property of this class,
			$fields = array();
			$fields[] = $this->committee_id ? "committee_id='{$this->committee_id}'" : "committee_id=null";
			$fields[] = $this->resumePath ? "resumePath='{$this->resumePath}'" : "resumePath=null";
			$fields[] = $this->firstname ? "firstname='{$this->firstname}'" : "firstname=null";
			$fields[] = $this->lastname ? "lastname='{$this->lastname}'" : "lastname=null";
			$fields[] = $this->email ? "email='{$this->email}'" : "email=null";
			$fields[] = $this->address ? "address='{$this->address}'" : "address=null";
			$fields[] = $this->city ? "city='{$this->city}'" : "city=null";
			$fields[] = $this->zipcode ? "zipcode='{$this->zipcode}'" : "zipcode=null";
			$fields[] = $this->homePhone ? "homePhone='{$this->homePhone}'" : "homePhone=null";
			$fields[] = $this->workPhone ? "workPhone='{$this->workPhone}'" : "workPhone=null";
			$fields[] = $this->resident ? "resident='{$this->resident}'" : "resident=null";
			$fields[] = $this->occupation ? "occupation='{$this->occupation}'" : "occupation=null";
			$fields[] = $this->interest ? "interest='{$this->interest}'" : "interest=null";
			$fields[] = $this->qualifications ? "qualifications='{$this->qualifications}'" : "qualifications=null";
			$fields[] = $this->timestamp ? "timestamp='{$this->timestamp}'" : "timestamp=null";

			$fields = implode(",",$fields);


			if ($this->id) { $this->update($fields); }
			else { $this->insert($fields); }
		}

		private function update($fields)
		{
			global $PDO;

			$sql = "update applications set $fields where id={$this->id}";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
		}

		private function insert($fields)
		{
			global $PDO;

			$sql = "insert applications set $fields";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
			$this->id = $PDO->lastInsertID();
		}


		public function getId() { return $this->id; }
		public function getCommitteeId() { return $this->committee_id; }
		public function getResumePath() { return $this->resumePath; }
		public function getFirstname() { return $this->firstname; }
		public function getLastname() { return $this->lastname; }
		public function getEmail() { return $this->email; }
		public function getAddress() { return $this->address; }
		public function getCity() { return $this->city; }
		public function getZipcode() { return $this->zipcode; }
		public function getHomePhone() { return $this->homePhone; }
		public function getWorkPhone() { return $this->workPhone; }
		public function getResident() { return $this->resident; }
		public function getOccupation() { return $this->occupation; }
		public function getInterest() { return $this->interest; }
		public function getQualifications() { return $this->qualifications; }
		public function getTimestamp() { return $this->timestamp; }


		public function setId($int) { $this->id = $int; }
		public function setCommitteeId($int) { $this->committee_id = $int; }
		public function setResumePath($int) { $this->resumePath = $int; }
		public function setFirstname($string) { $this->firstname = sanitizeString($string); }
		public function setLastname($string) { $this->lastname = sanitizeString($string); }
		public function setEmail($string) { $this->email = sanitizeString($string); }
		public function setAddress($string) { $this->address = sanitizeString($string); }
		public function setCity($string) { $this->city = sanitizeString($string); }
		public function setZipcode($int) { $this->zipcode = $int; }
		public function setHomePhone($string) { $this->homePhone = sanitizeString($string); }
		public function setWorkPhone($string) { $this->workPhone = sanitizeString($string); }
		public function setResident($int) { $this->resident = $int; }
		public function setOccupation($string) { $this->occupation = sanitizeString($string); }
		public function setInterest($text) { $this->interest = sanitizeString($text); }
		public function setQualifications($text) { $this->qualifications = sanitizeString($text); }
		public function setTimestamp($timestamp) { $this->timestamp = $timestamp; }

		public function gzip($srcFileName, $dstFileName)
		{
   		// getting file content
   		$fp = fopen($srcFileName, "r");
   		$data = fread ($fp, filesize($srcFileName));
   		fclose($fp);
  
 		  // writing compressed file
   		$zp = gzopen($dstFileName, "w9");
   		gzwrite($zp, $data);
   		gzclose($zp);
		}
		
		public function deleteApplication() 
		{
			global $PDO;
			
			if ($this->resumePath)
			{
				unlink($this->resumePath);
				$dir = explode(basename($this->resumePath), $this->resumePath);
				rmdir($dir[0]);
			}
			$sql = "delete from user_applications where application_id ={$this->id}";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }

			$sql = "delete from applications where id={$this->id}";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
		}
	}
?>