<?php

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\DuplicateException;
	use CzProject\SqlGenerator\Drivers;
	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;


	class DropIndex implements IStatement
	{
		/** @var string|NULL */
		private $index;


		/**
		 * @param  string|NULL
		 */
		public function __construct($index)
		{
			$this->index = $index;
		}


		/**
		 * @return string
		 */
		public function toSql(IDriver $driver)
		{
			if ($this->index === NULL) { // PRIMARY KEY
				if ($driver instanceof Drivers\MysqlDriver) {
					return 'DROP PRIMARY KEY';

				} else {
					throw new \CzProject\SqlGenerator\NotImplementedException('Drop of primary key is not implemented for driver ' . get_class($driver) . '.');
				}
			}

			return 'DROP INDEX ' . $driver->escapeIdentifier($this->index);
		}
	}
