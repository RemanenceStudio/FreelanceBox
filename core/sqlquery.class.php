<?php

class SQLQuery 
{
    
	protected $_dbHandle;
    protected $_result;

    /**
     * connect to the database
     */

    function connect($address, $account, $pwd, $name)
	{
        $this->_dbHandle = @mysql_connect($address, $account, $pwd);
        
		if ($this->_dbHandle != 0) 
		{
            if (mysql_select_db($name, $this->_dbHandle)) 
			{
                return $this->_dbHandle;
            }
            else 
			{
                return 0;
            }
        }
        else 
		{
            return 0;
        }
    }

    
    /**
     * Disconnect from the database
     */

    function disconnect() 
	{
        if (@mysql_close($this->_dbHandle) != 0) 
		{
            return 1;
        }  
		else 
		{
            return 0;
        }
    }
    
    /**
     * Select all rows in a table
     */
    function selectAll($criteria) 
	{
    	$query = 'select * from `'.$this->_table.'` ' . $criteria;
    	return $this->query($query);
    }

	
    
	/**
	 * Execute a sql query
	 */
	function query($query, $singleResult = 0) 
	{

		$this->_result = mysql_query($query, $this->_dbHandle);
		
		if (!$this->_result)
		{
			if (DEVELOPMENT_ENVIRONMENT == true)
				echo "<br/>".$query;
			trigger_error('Invalid Query: '. mysql_error(), E_USER_ERROR);
		
		}
		
		$result = '';
		if (preg_match("/select/i",$query)) 
		{
			while ($row = mysql_fetch_assoc($this->_result)) 
			{
				$result[] = $row;
			}
            
			return $result;
		}
		else if (preg_match("/insert/i",$query))
		{
			return mysql_insert_id($this->_dbHandle);
		}
		else return true;
		

	}

    function pre($data)
    {
         echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}
