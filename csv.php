<?php

	class csv
	{

		private $m_csv;

		private $m_count;

		private $m_index;

		private $m_rows;

		private function row()
		{
			$row = [];
			$column = '';
			while ($this->m_index < $this->m_count)
			{
				$character = $this->m_csv[$this->m_index++];
				if ($character == '"')
				{
					while ($this->m_index < $this->m_count)
					{
						$character = $this->m_csv[$this->m_index++];
						if ($character == '"')
						{
							/*
							 * If a double quote is found in a double quoted column then the following character must be:
							 *
							 *   - A double quote which is the second character of an escaped double quote.
							 *   - A comma which is the end of the column.
							 *   - A CR, CR LF or LF which is the end of the column and the end of the row.
							 *   - An EOF which is the end of the column, the end of the row and the end of the file.
							 *
							 * Anything else is an error.
							 */
							if ($this->m_index < $this->m_count)
							{
								$character = $this->m_csv[$this->m_index++];
								if ($character == '"')
								{
									$column .= $character;
								}
								else if ($character == ',')
								{
									$row[] = $column;
									$column = '';
									break;
								}
								else if ($character == "\r")
								{
									if ($this->m_index < $this->m_count)
									{
										$character = $this->m_csv[$this->m_index];
										if ($character == "\n")
										{
											$this->m_index++;
										}
									}
									$row[] = $column;
									return $row;
								}
								else if ($character == "\n")
								{
									$row[] = $column;
									return $row;
								}
								else
								{
									throw new Exception('Unescaped double quote found in double quote enclosed column.');
								}
							}
							else
							{
								$row[] = $column;
								return $row;
							}
						}
						else
						{
							$column .= $character;
						}
					}
					if ($this->m_index == $this->m_count)
					{
						throw new Exception('Unterminated double quoted column found.');
					}
				}
				else
				{
					while (true)
					{
						if ($character == ',')
						{
							$row[] = $column;
							$column = '';
							break;
						}
						else if ($character == "\r")
						{
							if ($this->m_index < $this->m_count)
							{
								$character = $this->m_csv[$this->m_index];
								if ($character == "\n")
								{
									$this->m_index++;
								}
							}
							$row[] = $column;
							return $row;
						}
						else if ($character == "\n")
						{
							$row[] = $column;
							return $row;
						}
						else
						{
							$column .= $character;
						}
						if ($this->m_index < $this->m_count)
						{
							$character = $this->m_csv[$this->m_index++];
						}
						else
						{
							$row[] = $column;
							return $row;
						}
					}
				}
			}
		}

		public function __construct($p_csv)
		{
			$this->m_csv = $p_csv;
			$this->m_count = strlen($this->m_csv);
			$this->m_index = 0;
		}

		public function rows()
		{
			if (!isset($this->m_rows))
			{
				$this->m_rows = [];
				while ($this->m_index < $this->m_count)
				{
					$this->m_rows[] = $this->row();
				}
			}
			return $this->m_rows;
		}

	}
