<?php

class B2DB
{
	static public function getTable($tablename)
	{
		return new B2DB();
	}

	public function getByID($id)
	{
		return new \b2db\Row();
	}
}