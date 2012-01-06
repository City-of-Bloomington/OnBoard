<?php
/**
 * @copyright 20011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

interface ExternalIdentity
{
	/**
	 * Should load user data from storage
	 */
	public function __construct($username);

	/**
	 * Return whether the username, password combo is valid
	 *
	 * @param string $username
	 * @param string $password The unencrypted password
	 * @return bool
	 */
	public static function authenticate($username,$password);

	/**
	 * @return string
	 */
	public function getFirstname();

	/**
	 * @return string
	 */
	public function getLastname();

	/**
	 * @return string
	 */
	public function getEmail();

}