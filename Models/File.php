<?php
/**
 * Files will be stored as /data/{tablename}/YYYY/MM/DD/$file_id.ext
 * User provided filenames will be stored in the database
 *
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

abstract class File extends ActiveRecord
{
    protected $tempFile;
    protected $newFile;

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

		#'jpg' =>['mime_type'=>'image/jpeg'],
		#'gif' =>['mime_type'=>'image/gif' ],
		#'png' =>['mime_type'=>'image/png' ],
		#'tiff'=>['mime_type'=>'image/tiff']
		#'pdf' =>['mime_type'=>'application/pdf'],
		#'rtf' =>['mime_type'=>'application/rtf'],
		#'doc' =>['mime_type'=>'application/msword'],
		#'xls' =>['mime_type'=>'application/msexcel'],
		#'gz'  =>['mime_type'=>'application/x-gzip'],
		#'zip' =>['mime_type'=>'application/zip'],
		#'txt' =>['mime_type'=>'text/plain'],
		#'wmv' =>['mime_type'=>'video/x-ms-wmv'],
		#'mov' =>['mime_type'=>'video/quicktime'],
		#'rm'  =>['mime_type'=>'application/vnd.rn-realmedia'],
		#'ram' =>['mime_type'=>'audio/vnd.rn-realaudio'],
		#'mp3' =>['mime_type'=>'audio/mpeg'],
		#'mp4' =>['mime_type'=>'video/mp4'],
		#'flv' =>['mime_type'=>'video/x-flv'],
		#'wma' =>['mime_type'=>'audio/x-ms-wma'],
		#'kml' =>['mime_type'=>'application/vnd.google-earth.kml+xml'],
		#'swf' =>['mime_type'=>'application/x-shockwave-flash'],
		#'eps' =>['mime_type'=>'application/postscript'],
    ];

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$zend_db = Database::getConnection();
				if (ActiveRecord::isId($id)) {
					$sql = "select * from {$this->tablename} where id=?";
				}
				// Internal filename without extension
				elseif (ctype_xdigit($id)) {
					$sql = "select * from {$this->tablename} where internalFilename like ?";
					$id.= '%';
				}
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('files/unknownFile');
				}
			}
		}
		else {
            // Default Values

            // Set temporary timestamps
            //
            // These values are only used for initial creation of empty File objects.
            // Once the file is saved to the database, we let MySQL handle generating
            // actual timestamps for these values.
            //
            // We need to have these values, because we usually use the date portion
            // as the path to store the files.  As long as we are only looking
            // at the date portion, we should be okay.  The time portion of these fields
            // will change when the record is saved to the database.  (It should really
            // only ever differ by a matter of seconds)
			$now = new \DateTime();
			$this->data['created'] = $now->format(ActiveRecord::MYSQL_DATETIME_FORMAT);
			$this->data['updated'] = $now->format(ActiveRecord::MYSQL_DATETIME_FORMAT);
		}
	}

	public function save()
	{
        // Let MySQL handle setting correct datetimes for created and updated
        if ($this->getId()) {
            unset($this->data['created']);
        }
        unset($this->data['updated']);

        // Move the new file into place
        if ($this->tempFile && $this->newFile) {
            $this->saveFile($this->tempFile, $this->newFile);

            if ($this->data['mime_type'] != 'application/pdf') {
                self::convertToPDF($this->newFile);

                $extension = self::$mime_types[$this->data['mime_type']];
                $this->data['filename' ] = basename($this->data['filename'], $extension).'pdf';
                $this->data['mime_type'] = 'application/pdf';
            }
        }

        parent::save();
    }

    protected function saveFile($tempFile, $newFile)
    {
        $directory = dirname($newFile);
        if (!is_dir($directory)) {
            mkdir  ($directory, 0777, true);
        }
        rename($tempFile, $newFile);
        chmod($newFile, 0666);

        // Check and make sure the file was saved
        if (!is_file($newFile)) {
            throw new \Exception('files/badServerPermissions');
        }
    }

	/**
	 * Deletes the file from the hard drive
	 */
	public function delete()
	{
		if ($this->getId()) {
			$zend_db = Database::getConnection();

			unlink($this->getFullPath());
			parent::delete();
		}
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id');          }
	public function getFilename()     { return parent::get('filename');    }
	public function getMime_type()    { return parent::get('mime_type');   }
	public function getCreated($f=null, \DateTimeZone $tz=null) { return parent::getDateData('created', $f, $tz); }
	public function getUpdated($f=null, \DateTimeZone $tz=null) { return parent::getDateData('updated', $f, $tz); }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Populates this object by reading information on a file
	 *
	 * This function does the bulk of the work for setting all the required information.
	 * It tries to read as much meta-data about the file as possible
	 *
	 * @param array|string Either a $_FILES array or a path to a file
	 */
	public function setFile($file)
	{
		// Handle passing in either a $_FILES array or just a path to a file
		$this->tempFile = is_array($file) ? $file['tmp_name']       : $file;
		$filename       = is_array($file) ? basename($file['name']) : basename($file);
		if (!$this->tempFile) {
			throw new \Exception('files/uploadFailed');
		}

		$this->data['mime_type'] = mime_content_type($this->tempFile);
		if (array_key_exists($this->data['mime_type'], self::$mime_types)) {
            $extension = self::$mime_types[$this->data['mime_type']];
		}
		else {
			throw new \Exception("{$this->tablename}/unknownFileType");
		}

		// Clean all bad characters from the filename
		$filename = $this->createValidFilename($filename, $extension);
		$this->data['filename'] = $filename;

		// Flag where it's supposed to go
		// Actually moving the file is deferred until save()
		$this->newFile = $this->getFullPath();
	}

	/**
	 * In-place conversion of given file to PDF
	 *
	 * You must have set the SOFFICE path in bootstrap.inc
	 * Apache must have permission to write to the SITE_HOME directory.
	 * LibreOffice will create .config and .cache directories in SITE_HOME
	 *
	 * @param string $file Full path to the file to convert
	 */
	public static function convertToPDF($file)
	{
        if ($file && is_file($file) && is_writable($file)) {
            $info = pathinfo($file);
            $dir  = $info['dirname'];

            $cmd  = 'export HOME='.SITE_HOME.' && '.SOFFICE." --convert-to pdf --headless --outdir $dir $file";
            $out  = "$cmd\n";
            echo $out;
            $out .= shell_exec($cmd);
            if (is_file("$file.pdf")) {
                 rename("$file.pdf", $file);
            }
            else {
                file_put_contents(SITE_HOME.'/soffice.log', $out, FILE_APPEND);
                unlink($file);
                throw new \Exception("{$this->tablename}/pdfConversionFailed");
            }
        }
        else {
            throw new \Exception("{$this->tablename}/pdfConversionFailed");
        }
	}

	/**
	 * Returns the partial path of the file, relative to /data/files
	 *
	 * Implementations of this class will usually override this function
	 * with their own custom scheme for the directory structure.
	 * This implementation should be a good enough default that most
	 * of the time, we won't need to override it.
	 *
	 * @return string
	 */
	public function getDirectory()
	{
		return $this->getCreated('Y/m/d');
	}

	/**
	 * Returns path to the file, relative to /data/files
	 *
	 * We do not use the filename the user chose when saving the files.
	 * We generate a unique filename the first time the filename is needed.
	 * This filename will be saved in the database whenever this file is
	 * finally saved.
	 *
	 * @return string
	 */
	public function getInternalFilename()
	{
		$filename = parent::get('internalFilename');
		if (!$filename) {
			$filename = $this->getDirectory().'/'.uniqid();
			parent::set('internalFilename', $filename);
		}
		return $filename;
	}

	/**
	 * @return int
	 */
	public function getFilesize()
	{
		return filesize($this->getFullPath());
	}

	/**
	 * Cleans a filename of any characters that might cause problems on filesystems
	 *
	 * If an new extension is provided, the filename's extension will be replaced
	 * with the provided extension.
	 *
	 * @param string $filename
	 * @param string $extension  Optional, new extension to use for the filename
	 * @return string
	 */
	public static function createValidFilename($filename, $extension=null)
	{
		// No bad characters
		$filename = preg_replace('/[^A-Za-z0-9_\-\.\s]/','',$filename);

		// Convert spaces to underscores
		$filename = preg_replace('/\s+/','_',$filename);

		// Lower case any file extension
		if (preg_match('/(^.*)\.([^\.]+)$/',$filename,$matches)) {
            $filename = $extension
                ? $matches[1].'.'.$extension
                : $matches[1].'.'.strtolower($matches[2]);
		}

		return $filename;
	}

	/**
	 * Returns the full path to the file or derivative
	 *
	 * @return string
	 */
	public function getFullPath()
	{
        return SITE_HOME."/{$this->tablename}/{$this->getInternalFilename()}";
	}
}
