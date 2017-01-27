<?php
/**
 * Files will be stored as /data/applicantFiles/YYYY/MM/DD/$file_id.ext
 * User provided filenames will be stored in the database
 *
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class ApplicantFile extends ActiveRecord
{
    use File;

	protected $tablename = 'applicantFiles';
	protected $applicant;

	/**
	 * Whitelist of accepted file types
	 */
    public static $mime_types = [
        'application/msword'                                                      => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.oasis.opendocument'                                      => 'odf',
        'application/vnd.oasis.opendocument.text'                                 => 'odt',
        'application/pdf'                                                         => 'pdf',
        'application/rtf'                                                         => 'rtf'
    ];

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->getFilename())      { throw new \Exception('files/missingFilename');  }
		if (!$this->getMime_type())     { throw new \Exception('files/missingMimeType');  }
		if (!$this->getApplicant_id())  { throw new \Exception('files/missingApplicant'); }
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getApplicant_id() { return parent::get('applicant_id');   }
	public function getApplicant()    { return parent::getForeignKeyObject(__namespace__.'\Applicant', 'applicant_id'); }

	public function setApplicant_id($i) { parent::setForeignKeyField (__namespace__.'\Applicant', 'applicant_id', $i); }
	public function setApplicant   ($o) { parent::setForeignKeyObject(__namespace__.'\Applicant', 'applicant_id', $o);  }

	/**
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
        $this->setApplicant_id($post['applicant_id']);
	}
}
