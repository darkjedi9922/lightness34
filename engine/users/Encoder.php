<?php namespace engine\users;

class Encoder
{
	public static function getPassword(string $password): string
    {
		return md5($password);
	}

	public static function getSid(string $login, string $encoded_password): string
    {
		return md5($login.$encoded_password);
	}
}