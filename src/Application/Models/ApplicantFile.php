<?php
/**
 * Files will be stored as /data/applicantFiles/YYYY/MM/DD/$file_id.ext
 * User provided filenames will be stored in the database
 *
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;

class ApplicantFile extends File
{
    protected $tablename = 'applicantFiles';
    protected $person;

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
     * Check information not related to the file storage
     */
    public function validateDatabaseInformation()
    {
        if (!$this->getPerson_id()) { throw new \Exception('files/missingApplicant'); }
    }

    public function validate()
    {
        $this->validateDatabaseInformation();

        parent::validate();
    }

    //----------------------------------------------------------------
    // Generic Getters & Setters
    //----------------------------------------------------------------
    public function getPerson_id()   { return parent::get('person_id');   }
    public function getPerson()      { return parent::getForeignKeyObject(__namespace__.'\Person', 'person_id'); }

    public function setPerson_id($i) { parent::setForeignKeyField (__namespace__.'\Person', 'person_id', $i); }
    public function setPerson   ($o) { parent::setForeignKeyObject(__namespace__.'\Person', 'person_id', $o);  }
}
