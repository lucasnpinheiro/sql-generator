<?php

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\OutOfRangeException;
	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;


	class IndexDefinition implements IStatement
	{
		const TYPE_INDEX = 'INDEX';
		const TYPE_PRIMARY = 'PRIMARY';
		const TYPE_UNIQUE = 'UNIQUE';
		const TYPE_FULLTEXT = 'FULLTEXT';

		/** @var string|NULL */
		private $name;

		/** @var string */
		private $type;

		/** @var IndexColumnDefinition[] */
		private $columns = array();


		/**
		 * @param  string|NULL
		 * @param  string
		 */
		public function __construct($name = NULL, $type)
		{
			$this->name = $name;
			$this->setType($type);
		}


		/**
		 * @param  string
		 * @param  string
		 * @param  int|NULL
		 * @return static
		 */
		public function addColumn($column, $order = IndexColumnDefinition::ASC, $length = NULL)
		{
			$this->columns[] = new IndexColumnDefinition($column, $order, $length);
			return $this;
		}


		/**
		 * @param  string
		 * @return void
		 */
		private function setType($type)
		{
			$type = (string) $type;
			$exists = $type === self::TYPE_INDEX
				|| $type === self::TYPE_PRIMARY
				|| $type === self::TYPE_UNIQUE
				|| $type === self::TYPE_FULLTEXT;

			if (!$exists) {
				throw new OutOfRangeException("Index type '$type' not found.");
			}

			$this->type = $type;
		}


		/**
		 * @return string
		 */
		public function toSql(IDriver $driver)
		{
			$output = $this->type !== self::TYPE_INDEX ? ($this->type . ' ') : '';
			$output .= 'KEY';

			if ($this->name !== NULL) {
				$output .= ' ' . $driver->escapeIdentifier($this->name);
			}

			$output .= ' (';
			$isFirst = TRUE;

			foreach ($this->columns as $column) {
				if ($isFirst) {
					$isFirst = FALSE;

				} else {
					$output .= ', ';
				}

				$output .= $column->toSql($driver);
			}

			$output .= ')';
			return $output;
		}
	}
