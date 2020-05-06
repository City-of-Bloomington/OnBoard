<?php
/**
 * @copyright 2011-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web\Auth;

interface ExternalIdentity
{
	/**
	 * Should load user data from storage
	 */
	public function __construct(string $username);

	/**
	 * Return whether the username, password combo is valid
	 * @throws Exception
	 */
	public static function authenticate(string $username, string $password): bool;

	/**
	 * @return string
	 */
	public function getFirstname();
	public function getLastname();
	public function getEmail();
	public function getPhone();
	public function getAddress();
	public function getCity();
	public function getState();
	public function getZip();
}
