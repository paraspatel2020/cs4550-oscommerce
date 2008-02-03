<?php
class DobaLog {
	var $fields = array();
	
	function DobaLog() {}
	
	function load($doba_log_id) {
		$doba_log_id = intval($doba_log_id);
		
		$sql = 'select * from DobaLog where doba_log_id='.$doba_log_id;
		$cnt_query = tep_db_query($sql);
  		$o = tep_db_fetch_array($cnt_query);
		
		if (isset($o['doba_log_id']) && $o['doba_log_id'] == $doba_log_id) {
			$this->fields = $o;
			
			return true;
		}
		
		return false;
	}
	
	function store() {
		$new_entry = true;
		if (isset($this->fields['doba_log_id']) && $this->fields['doba_log_id'] > 0) {
			$new_entry = false;
		}
			
		if ($new_entry) {
			$sql = 'insert into DobaLog set ';
		} else {
			$sql = 'update DobaLog set ';
		}
		
		$cnt = 0;
		foreach ($this->fields as $i => $f) {
			if ($cnt > 0) {
				$sql .= ',';
			}
			$sql .= $i . '="' . $f . '"';
			++$cnt;
		}
		
		if (!$new_entry) {
			$sql .= ' where doba_log_id='.$this->fields['doba_log_id'];
		}
		
		if ($cnt > 0) {
			$store = tep_db_query($sql);
			$this->doba_log_id(tep_db_insert_id());
			
			return true;
		}
		
		return false;
	}
	
	function doba_log_id($arg=null) {
		if (!is_null($arg)) {
			$this->fields['doba_log_id'] = intval($arg);
		}
		return isset($this->fields['doba_log_id']) ? intval($this->fields['doba_log_id']) : null;
	}
	
	function datatype($arg=null) {
		if (!is_null($arg)) {
			$this->fields['datatype'] = trim($arg);
		}
		return isset($this->fields['datatype']) ? trim($this->fields['datatype']) : null;
	}
	
	function local_id($arg=null) {
		if (!is_null($arg)) {
			$this->fields['local_id'] = intval($arg);
		}
		return isset($this->fields['local_id']) ? intval($this->fields['local_id']) : null;
	}
	
	function xfer_method($arg=null) {
		if (!is_null($arg)) {
			$this->fields['xfer_method'] = trim($arg);
		}
		return isset($this->fields['xfer_method']) ? trim($this->fields['xfer_method']) : null;
	}
	
	function ymdt($arg=null, $as_timestamp=false) {
		if (!is_null($arg)) {
			if ($as_timestamp) {
				$this->fields['ymdt'] = date('Y-m-d H:i:s', $arg);
			} else {
				$this->fields['ymdt'] = date('Y-m-d H:i:s', strtotime(trim($arg)));
			}
		}
		return isset($this->fields['ymdt']) ? trim($this->fields['ymdt']) : null;
	}
	
	function filename($arg=null) {
		if (!is_null($arg)) {
			$this->fields['filename'] = trim($arg);
		}
		return isset($this->fields['filename']) ? trim($this->fields['filename']) : null;
	}
	
	function api_response($arg=null) {
		if (!is_null($arg)) {
			$this->fields['api_response'] = trim($arg);
		}
		return isset($this->fields['api_response']) ? trim($this->fields['api_response']) : null;
	}
	
	function logOrderDownload($objDobaOrders, $filename, $time) {
		foreach ($objDobaOrders->orders as $o) {
			$dl = new DobaLog();
			$dl->datatype('order');
			$dl->local_id($o->po_number());
			$dl->xfer_method('file');
			$dl->ymdt($time, true);
			$dl->filename($filename);
			$dl->store();
		}
		
		return;
	}
	
	function getLogHistorySummary($datatype) {
		$datatype = trim($datatype);
		$ret = array();
		
		$sql = 'select 
					ymdt, xfer_method, filename, api_response, count(*) as order_cnt 
				from 
					DobaLog 
				where 
					datatype="' . $datatype .'" group by ymdt';
		$history_query = tep_db_query($sql);
		while ($o = tep_db_fetch_array($history_query)) {
			$ret[] = $o;
		}
		
		return $ret;		
	}
}  
?>
