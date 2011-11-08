<?php

class B2DB
{
	public static function getTable($tablename)
	{
		return new B2DB();
	}

	public function getByID($id)
	{
		return new \b2db\Row();
	}
}