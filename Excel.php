<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Excel {

	public $filename 		= 'excel-doc';
	public $custom_titles;

	public function make_from_db($db_results) {
		$data 		= NULL;
		$fields 	= $db_results->field_data();

		if ($db_results->num_rows() == 0) {
			show_error('The table appears to have no data');
		}
		else {
			$headers = $this->titles($fields);

			foreach ($db_results->result() AS $row) {
				$line = '';
				foreach ($row AS $value) {
					if (!isset($value) OR $value == '') {
						$value = "\t";
					}
					else {
						$value = str_replace('"', '""', $value);
						$value = '"' . $value . '"' . "\t";
					}
					$line .= $value;
				}
				$data .= trim($line) . "\n";
			}
			$data = str_replace("\r", "", $data);

			$this->generate($headers, $data);
		}
	}

	public function make_from_array($titles, $array) {
		$data = NULL;

		if (!is_array($array)) {
			show_error('The data supplied is not a valid array');
		}
		else {
			$headers = $this->titles($titles);

			if (is_array($array)) {
				foreach ($array AS $row) {
					$line = '';
					foreach ($row AS $value) {
						if (!isset($value) OR $value == '') {
							$value = "\t";
						}
						else {
							$value = str_replace('"', '""', $value);
							$value = '"' . $value . '"' . "\t";
						}
						$line .= $value;
					}
					$data .= trim($line) . "\n";
				}
				$data = str_replace("\r", "", $data);

				$this->generate($headers, $data);
			}
		}
	}

	private function generate($headers, $data) {
		$this->set_headers();

		echo "$headers\n$data";  
	}

	public function titles($titles) {
		if (is_array($titles)) {
			$headers = array();

			if (is_null($this->custom_titles)) {
				if (is_array($titles)) {
					foreach ($titles AS $title) {
						$headers[] = $title;
					}
				}
				else {
					foreach ($titles AS $title) {
						$headers[] = $title->name;
					}
				}
			}
			else {
				$keys = array();
				foreach ($titles AS $title) {
					$keys[] = $title->name;
				}
				foreach ($keys AS $key) {
					$headers[] = $this->custom_titles[array_search($key, $keys)];
				}
			}

			return implode("\t", $headers);
		}
	}

	private function set_headers() {
		header("Pragma: public");
	    header("Expires: 0");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Content-Type: application/force-download");
	    header("Content-Type: application/octet-stream");
	    header("Content-Type: application/download");;
	    header("Content-Disposition: attachment;filename=$this->filename.xls");
	    header("Content-Transfer-Encoding: binary ");
	}
}