<?php
class Model extends SQLQuery
{
    protected $_model;
    public $CONFIG;
    /**
     * Connect to database and initialize model
     */
    function __construct()
    {
        $this->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $this->_model = get_class($this);
        $this->_table = strtolower($this->_model) . "s";

        global $CONFIG;
        $this->CONFIG = &$CONFIG;
    }

    /**
     * Paginate model based on criteria
     */
    function paginate($current_page = 1, $per_page = 10, $criteria = null, $order = null, $table = null, $fields = null, $join=null)
    {
        $fields = ($fields==null)?'*':$fields;

        $table = ($table == null)?$table = $this->_table:$table;

        $current_page = ($current_page < 1)?1:$current_page;

        $join_clause = ($join == null)?'':" ".$join;

        $where_clause = ($criteria == null)?'':" WHERE " . $criteria;

        $order_clause = ($order == null)?' ORDER BY created DESC':" ORDER BY " . $order;

        $offset = ($current_page - 1) * $per_page;


        /**
         * Perform the query on the database
         */
        $pagination['page'] = $this->query("SELECT SQL_CALC_FOUND_ROWS $fields FROM " . $table . $join_clause. $where_clause . $order_clause . " LIMIT " . $offset . " , " . $per_page);

        /**
         * Determine the total number of pages
         * based on the number of items per
         * page
         */
        $total_rows = $this->query("SELECT FOUND_ROWS()");
        $total_rows = $total_rows[0]['FOUND_ROWS()'];

        $pagination['total_pages'] = ceil($total_rows / $per_page);
        $pagination['current_page'] = $current_page;

        return $pagination;
    }


    /**
     * Edit a records details
     */
    function edit($field_list, $criteria, $table = null)
    {
        if (!is_array($field_list))
        {
            return false;
        }

        if ($table == null)
        {
            $table = $this->_table;
        }


        $set_clause = "SET ";
        foreach ($field_list as $field)
        {
            $set_clause .= key($field_list) . " = '" . $field . "', ";
            next($field_list);
        }
        $set_clause = rtrim($set_clause, ', ');

        $sql = "UPDATE " . $table . " " . $set_clause . " WHERE " . $criteria;

        $result = $this->query($sql);

        if ($result)
        {
            return $result;
        }
        else return false;
    }


    /**
     * Create an object for the required
     * model
     */
    function loadModel($model)
    {
        $this->$model = new $model;
    }


    function __destruct()
    {
    }
}
