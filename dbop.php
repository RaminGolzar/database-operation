<?php

// this class name is a Database Operation (DBOP)
// write by PHP v7.4 & SQL v8

class dbop extends PDO
{

    private $result; // finally result - result prepare SQL
// =========================

    /* query string components.
     * for select & delete & so on query
     */
    public $sql = [
        'statement' => null ,
        'func' => null ,
        'column' => null ,
        'case' => null ,
        'from' => null ,
        'on' => null ,
        'set' => null , // for update statement
        'values' => null , // for insert into
        'where' => null ,
        'limit' => null ,
        'group_by' => null ,
        'order_by' => null ,
    ];

// =========================

    public function __construct (string $dbName = null , string $host = 'localhost' , string $userName = 'root' , string $password = null) {
        try {
            if ($dbName) {
                parent::__construct ("mysql:host=$host;dbname=$dbName" , $userName , $password);
            } else {
                parent::__construct ("mysql:host=$host" , $userName , $password);
            }

            return true;
        } catch (PDOException $e) {
            return false;
            // $exc->getMessage()
        }
    }

// =========================

    private function neutralize_input ($input) {
        if (is_string ($input) || is_int ($input)) {
            return $this->neutralize_var ($input);
        } else if (is_array ($input)) {
            return $this->neutralize_array ($input);
        }
    }

    private function neutralize_var (string $input): string {
        return trim (stripcslashes (htmlspecialchars ($input , ENT_QUOTES , 'UTF-8')));
    }

    private function neutralize_array (array $input): array {
        $na = [];

        foreach ($input as $k => $v) {
            $na[] = trim (stripcslashes (htmlspecialchars ($v , ENT_QUOTES , 'UTF-8')));
        }

        return $na;
    }

// =========================

    private const STR_COLUMNS = 0; // return string of columns name
    private const STR_VALUES = 1; // return string of values name
    private const STR_PARAMS = 2; // return string of parameters
    private const STR_COL_PARAM = 4; // return string of column & params by equal operator
    private const ARR_COLUMNS = 6; // return array of columns
    private const ARR_VALUES = 7; // return array of values
    private const ARR_PARAMS = 8; // return array of parameters
    private const ARR_CVP = 10; // return array of column, value, parameter (useful one component)

    // parse the columns, value, parameters
    private function parse (array $data , int $outputType , bool $filter = true) {
        switch ($outputType) {
            case self::STR_COLUMNS:
                return $this->str_columns ($data);

            case self::STR_VALUES:
                return $this->str_values ($data , $filter);

            case self::STR_PARAMS:
                return $this->str_parameters ($data);

            case self::STR_COL_PARAM:
                return $this->str_col_param ($data);

            case self::ARR_COLUMNS:
                return $this->arr_columns ($data);

            case self::ARR_VALUES:
                return $this->arr_values ($data , $filter);

            case self::ARR_PARAMS:
                return $this->arr_params ($data);

            case self::ARR_CVP:
                return $this->arr_cvp ($data , $filter);
        }
    }

    // return string of columns or parameters name
    private function str_columns (array $data): string {
        $c = '';

        for ($k = 0; $k < count ($data); $k++) {
            $row = each ($data);

            $c .= $row['key'] . ',';
        }

        return substr ($c , 0 , strlen ($c) - 1);
    }

    // return string of values name
    private function str_values (array $data , bool $filter = true): string {
        $v = '';

        for ($k = 0; $k < count ($data); $k++) {
            $row = each ($data);

            if ($filter) {
                $v .= $this->neutralize_input ($row[1]) . ',';
            } else {
                $v .= $row[1] . ',';
            }
        }

        return substr ($v , 0 , strlen ($v) - 1);
    }

    // return string of parameters name
    private function str_parameters (array $data): string {
        $p = '';

        for ($k = 0; $k < count ($data); $k++) {
            $row = each ($data);

            $p .= ':' . $row[0] . ',';
        }

        return substr ($p , 0 , strlen ($p) - 1);
    }

    // return string of columns & params
    private function str_col_param (array $data): string {
        $cp = '';

        // concate column & parameter
        for ($k = 0; $k < count ($data); $k++) {
            $row = each ($data);

            $cp .= $row[0] . '=:' . $row[0] . ',';
        }

        // remove last comma
        return substr ($cp , 0 , strlen ($cp) - 1);
    }

    // return array of columns
    private function arr_columns (array $data): array {
        return explode (',' , $this->str_columns ($data));
    }

    // return array of values
    private function arr_values (array $data , bool $filter = true): array {
        return explode (',' , $this->str_values ($data , $filter));
    }

    // return array of parameters
    private function arr_params (array $data): array {
        return explode (',' , $this->str_parameters ($data));
    }

    /* output this method:
     * 1- string of columns
     * 2- string of values
     * 3- string of parameters
     */

    private function arr_cvp (array $data , bool $filter = true): array {
        return [
            $this->str_columns ($data) ,
            $this->str_values ($data) ,
            $this->str_parameters ($data) ,
        ];
    }

// =========================
// SECTION ALTER TABLE
    # Auto Increment Offset
    public function aio (string $tblName , int $offset) {
        try {
            $this->result = $this->prepare ("ALTER TABLE $tblName AUTO_INCREMENT=$offset");
            $this->result->execute ();
        } catch (PDOException $e) {
            return false;
        }
    }

// =========================
    // drop default constraint
    public function drop_default (string $tblName , string $column) {
        try {
            $this->query = "ALTER TABLE $tblName ALTER $column DROP DEFAULT";

            return $this->run (self::RUN_PREPARE_EXECUTE);
        } catch (PDOException $e) {
            return false;
        }
    }

    // add default value in the alter table
    public function add_default (string $tblName , string $column , string $value) {
        try {
            $this->query = "ALTER TABLE $tblName ALTER $column SET DEFAULT '$value'";

            return $this->run (self::RUN_PREPARE_EXECUTE);
        } catch (PDOException $e) {
            return false;
        }
    }

    // failed
    public function drop_foreign_key (string $tblName , string $column) {
        try {
            $this->query = "ALTER TABLE $tblName DROP FOREIGN KEY ($column)";

            return $this->run (self::RUN_PREPARE_EXECUTE);
        } catch (PDOException $e) {
            return false;
        }
    }

    // add a foreign key into the column
    public function add_foreign_key (string $tblName , string $column , string $referencesTbl , string $referencesCol) {
        try {
            $this->query = "ALTER TABLE $tblName ADD FOREIGN KEY ($column) REFERENCES $referencesTbl($referencesCol)";

            return $this->run (self::RUN_PREPARE_EXECUTE);
        } catch (PDOException $e) {
            return false;
        }
    }

    // failed
    public function drop_primary_key (string $tblName) {
        try {
            $this->query = "ALTER TABLE $tblName DROP PRIMARY KEY";

            return $this->run (self::RUN_PREPARE_EXECUTE);
        } catch (PDOException $e) {
            return false;
        }
    }

// =========================
    // drop table
    public function delete_table (string $tblName) {
        try {
            $this->query = "DROP TABLE $tblName";

            $this->run (self::RUN_PREPARE_EXECUTE);
        } catch (PDOException $e) {
            return false;
        }
    }

// =========================
// SECTION CREATE TABLE
    // query for create table
    private $create_table = '';

    // set primary key
    // $data for check exists column
    private function primary_key (array $data , string $primaryKey) {
        $pk = explode (',' , $primaryKey);

        // one key
        if (count ($pk) == 1 && isset ($data[$pk[0]])) {
            return "PRIMARY KEY ($pk[0])";
        } else if (count ($pk) > 1) { // more key (index)
            $name = '';

            foreach ($pk as $v) {
                if (!isset ($data[$v])) {
                    return false;
                }

                // column must not underscore
                $name .= str_replace ('_' , '' , $v) . '_';
            }

            // remove last underscore
            $name = substr ($name , 0 , strlen ($name) - 1);

            return "CONSTRAINT $name PRIMARY KEY ($primaryKey)";
        }
    }

// ----------------------------------------
    // check constraint
    public function check (string $check) {
        if ($check) {
            $this->create_table .= ",CHECK ($check)";
        }

        return $this;
    }

    // foreign key constraint
    public function foreign_key (array $foreignKey) {
        if ($fk = each ($foreignKey)) {
            $this->create_table .= ",FOREIGN KEY ($fk[0]) REFERENCES $fk[1]";
        }

        return $this;
    }

    public function create_table (string $tblName , array $data , string $primaryKey) {
        $this->create_table = "CREATE TABLE $tblName (";

        // $k: column name
        // $v: data type & constraint
        foreach ($data as $k => $v) {
            $this->create_table .= "$k $v,";
        }

        // constraint
        $this->create_table .= $this->primary_key ($data , $primaryKey);

        return $this;
    }

// =========================

    public function show_databases (int $outputType = self::OP_PRINT_R) {
        try {
            $sql = "SHOW DATABASES";

            $this->result = $this->prepare ($sql);
            $this->result->execute ();

            return $this->output ($outputType);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function show_tables (int $outputType = self::OP_ARRAY) {
        try {
            $sql = "SHOW TABLES";

            $this->result = $this->prepare ($sql);
            $this->result->execute ();

            return $this->output ($outputType);
        } catch (PDOException $e) {
            return false;
        }
    }

// =========================

    public function create_database (string $dbName) {
        try {
            $sql = "CREATE DATABASE $dbName";

            $this->result = $this->prepare ($sql);
            $this->result->execute ();

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // drop database
    public function delete_database (string $dbName) {
        try {
            $this->query = "DROP DATABASE $dbName";

            $this->run (self::RUN_PREPARE_EXECUTE);
        } catch (PDOException $e) {
            return false;
        }
    }

// =========================

    public function query (string $query , int $outputType = self::OP_NO_CHANGE) {
        try {
            $this->result = $this->prepare ($query);
            $this->result->execute ();

            return $this->output ($outputType , true);
        } catch (PDOException $exc) {
            return false;
        }
    }

// =========================

    public function row_count (string $tblName , string $column = 'id' , int $outputType = self::OP_NO_CHANGE) {
        try {
            $this->select ()
                    ->from ($tblName)
                    ->count ($column)
                    ->run (self::RUN_SQL , $outputType , true);
        } catch (PDOException $e) {
            return false;
        }
    }

// =========================
    // $data include column & value
    // check for exists value
    public function value_exists (string $tblName , array $data) {
        try {
            $cvp = $this->parse ($data , self::ARR_CVP);
//
            return $this->select ($cvp[0])
                            ->from ($tblName)
                            ->where ([$cvp[1] => $cvp[0]])
                            ->run (self::RUN_SQL , self::OP_STRING);
        } catch (PDOException $e) {
            return false;
        }
    }

// =========================
    // add redord into the table
    public function add_row (string $tblName , array $data) {
        if (gettype (reset ($data)) == 'array') { // nested array
            $this->multiple_insert_record ($tblName , $data);
        } else if (gettype (reset ($data)) == 'string') { // one array
            $this->insert_record ($tblName , $data);
        }
    }

    // insert one record
    private function insert_record (string $tblName , array $data) {
        try {
            $cvp = $this->parse ($data , self::ARR_CVP);

            $this->sql['statement'] = "INSERT INTO $tblName ";
            $this->sql['column'] = "($cvp[0]) ";
            $this->sql['values'] = "VALUES ($cvp[2])";

            $this->parameters = $cvp[2];
            $this->values = $cvp[1];

            $this->run (self::RUN_SQL , self::OP_NO_CHANGE);
        } catch (PDOException $e) {
            return false;
        }
    }

    // insert multi record
    public function multiple_insert_record (string $tblName , array $data) {
        $e = reset ($data);

        $c = $this->parse ($e , self::STR_COLUMNS); // string of columns
        $p = $this->parse ($e , self::STR_PARAMS); // string of parameters

        $this->parameters = $p; // for binding

        try {
            $this->result = $this->prepare ("INSERT INTO $tblName ($c) VALUES ($p)");

            foreach ($data as $record) {
                $this->values = $this->parse ($record , self::STR_VALUES);

                $this->binding ();

                $this->result->execute ();
            }
        } catch (PDOException $e) {
            return false;
        }
    }

// =========================

    private $parameters = ''; // string of params - for binding
    private $values = ''; // string of values - for binding

    // next the prepare sql and previous the execute sql
    private function binding () {
        if (!$this->parameters || !$this->values) {
            return;
        }

        $p = explode (',' , $this->parameters); // array of parameters
        $v = explode (',' , $this->values); // array of values

        for ($k = 0; $k < count ($p); $k++) {
            $this->result->bindParam ($p[$k] , $v[$k]);
        }
    }

// =========================
    // union operator
    public function union (array $selectQuery , int $outputType = self::OP_NO_CHANGE , bool $echoQuery = false) {
        $sql = '';

        foreach ($selectQuery as $sq) {
            // for export table name
            $from = strpos ($sq , 'FROM') + 5;
            $nextSpace = strpos ($sq , ' ' , $from);
            $len = $nextSpace - $from;
            $tbl = substr ($sq , $from , $len);

            // for detect table result
            $sq = str_replace ('FROM' , ",'$tbl' table_name FROM" , $sq);

            $sql .= $sq . " UNION ";
        }

        // -7: dismiss last union word
        $this->finish (substr ($sql , 0 , strlen ($sql) - 7) , $outputType);
    }

// =========================
    // select statement
    public function select (string $column = null , bool $distinct = false) {
        $this->sql['statement'] = 'SELECT ';

        if ($column && $distinct) {
            $this->sql['column'] = "DISTINCT $column ";
        } else if ($column && !$distinct) {
            $this->sql['column'] = "$column ";
        } else if (!$column && !$distinct) {
            $this->sql['column'] = null;
        }

        return $this; // for chaining function
    }

    // join types
    public const INNER_JOIN = 'INNER';
    public const LEFT_JOIN = 'LEFT';
    public const RIGHT_JOIN = 'RIGHT';
    public const CROSS_JOIN = 'CROSS';

    // detect & set join type
    private $joinType = self::INNER_JOIN;

    // $on: condition
    // join keyword
    public function join ($tblName , string $on = null , string $joinType = self::INNER_JOIN) {
        $this->sql['from'] .= ',' . $tblName;

        $this->sql['on'] [] = $on;

        $this->joinType = $joinType;

        return $this;
    }

    // $condRes: Condition Result
    // case statement
    public function case (array $condRes , string $alias = null , string $else = null) {
        $sql = "CASE";

        // $k: condition - $v: return result.
        // conditions
        foreach ($condRes as $k => $v) {
            $v = $this->pwv ($v);

            $sql .= " WHEN $k THEN $v";
        }

        // else
        if ($else) {
            $else = $this->pwv ($else);
            $sql .= " ELSE $else";
        }

        // end
        $sql .= " END";

        // add alias
        if ($alias) {
            $sql .= " AS $alias ";
        }

        $this->sql['case'] = $sql;

        return $this;
    }

    // from statement
    public function from (string $tblName) {
        $this->sql['from'] = "FROM $tblName ";

        return $this; // for chaining function
    }

    // for where function - prepare string & null & int value
    # Prepare Where Value
    private function pwv ($value = null) {
        if (is_null ($value)) {
            return "'null'";
        } else if (is_int ($value) || is_numeric ($value)) {
            return $value;
        } else if (is_string ($value)) {
            return "'$value'";
        }
    }

    private $conditions = []; // for where clause
    private $combine_op = []; // combine operator for conditions in the where clause

    # Combine Operators - for $combine_op

    public const CO_AND = 'AND';
    public const CO_OR = 'OR';

    // $array: include column & value - baraye not kardan shart, bayad column (key) shamele (!) bashad. (*** for do not condition, must column include (!))
    // $operator: play each column and value (= , > , < , != , like , between , ...)
    // $combine_op: play each condition (and , or)
    // where statement
    public function where (array $array = [] , array $operator = [] , array $combine_op = []) {
        // set the default operator for each condition
        if (empty ($operator)) {
            for ($i = 0; $i <= count ($array); $i++) {
                $operator[] = '=';
            }
        }

        // assignment combine operator
        $this->combine_op = $combine_op;

        // generate condition by column & operator & value
        if ($array) {
            foreach ($array as $k => $v) {
                $op = each ($operator);

                // replace (!) with NOT operator
                $k = str_replace ('!' , 'NOT ' , $k);

                $this->conditions[] = $k . " $op[1] " . $this->pwv ($v) . ' ';
            }
        }

        return $this; // for chaining function
    }

    // for where clause - use in the run function
    # Conditions Concatenation
    private function cc () {
        $where = '';

        // set the default compination operator for two conditions
        if (empty ($this->combine_op)) {
            for ($i = 0; $i < count ($this->conditions) - 1; $i++) {
                $this->combine_op[] = 'AND';
            }
        }

        // condition concat
        for ($p = 0; $p < count ($this->conditions); $p++) {
            // be in dalil as each() estefadeh kardim, vaghti ke
            // tamame component haye conditions array ra unset()
            // mokonim, hala har vaght ke dobareh yek meghrar be
            // in array ezafeh konim andis as aval shoru nemishe va
            // as edameh shoru mishe
            $c = each ($this->conditions);
            $co = each ($this->combine_op);

            $where .= $c[1];

            if (isset ($co[1])) {
                $where .= ' ' . $this->combine_op[$p] . ' ';
            }
        }

        $this->sql['where'] = $where;
    }

    // the in operator in the where clause
    public function in (string $column , array $values , bool $not = false) {
        $where = '';

        if ($not) {
            $where = "$column NOT IN (";
        } else {
            $where = "$column IN (";
        }

        // values concatenation
        foreach ($values as $value) {
            $where .= $this->pwv ($value) . ',';
        }

        // replace last comma with parenthes
        $this->conditions[] .= substr ($where , 0 , strlen ($where) - 1) . ') ';

        return $this; // for chainin function
    }

    // dettect null value
    public function is_null (string $column , bool $isNull = true) {
        if ($isNull) {
            $this->conditions[] = "$column IS NULL ";
        } else {
            $this->conditions[] = "$column IS NOT NULL ";
        }

        return $this; // for chaining function
    }

    // the between operator in the where clause
    public function between (string $column , $value1 , $value2 , bool $not = false) {
        $value1 = $this->pwv ($value1);
        $value2 = $this->pwv ($value2);

        if ($not) {
            $this->conditions[] = "$column NOT BETWEEN $value1 AND $value2 ";
        } else {
            $this->conditions[] = "$column BETWEEN $value1 AND $value2 ";
        }

        return $this; // for chaining function
    }

    // order by statement
    public function order_by (string $column , bool $descending = false) {
        if ($descending) {
            $this->sql['order_by'] = "ORDER BY $column DESC ";
        } else {
            $this->sql['order_by'] = "ORDER BY $column ASC ";
        }

        return $this; // for chaining function
    }

    // limit statement
    public function limit (int $limit , int $offset = null) {
        if ($offset) {
            $this->sql['limit'] = "LIMIT $limit OFFSET $offset";
        } else {
            $this->sql['limit'] = "LIMIT $limit ";
        }

        return $this; // for chaining function
    }

    // group by statement
    public function group_by (string $column) {
        $this->sql['func'] = "COUNT($column) AS Count,";
        $this->sql['group_by'] = "GROUP BY $column ";

        return $this; // for chaining function
    }

    // min function
    public function min (string $column) {
        $this->sql['func'] .= "MIN($column) AS min_$column,";

        return $this; // for chaining function
    }

    // average function
    public function avg (string $column) {
        $this->sql['func'] .= "AVG($column) AS avg_$column,";

        return $this; // for chaining function
    }

    // sum function
    public function sum (string $column) {
        $this->sql['func'] .= "SUM($column) AS sum_$column,";

        return $this; // for chaining function
    }

    // max function
    public function max (string $column) {
        $this->sql['func'] .= "MAX($column) AS max_$column,";

        return $this; // for chaining function
    }

    public function if_null (string $column , string $value) {
        $this->sql['func'] .= "IFNULL($column , '$value') AS $column,";

        return $this;
    }

    // count function
    public function count (string $column) {
        $this->sql['func'] .= "COUNT($column) AS count_$column,";

        return $this; // for chaining function
    }

    // delete statement
    public function delete (string $tblName) {
        $this->sql['statement'] = "DELETE ";

        $this->from ($tblName);

        return $this;
    }

    public function update (string $tblName , array $newData) {
        $this->sql['statement'] = "UPDATE $tblName ";

        $this->sql['set'] = 'SET ' . $this->parse ($newData , self::STR_COL_PARAM) . ' ';

        $this->parameters = $this->parse ($newData , self::STR_PARAMS);
        $this->values = $this->parse ($newData , self::STR_VALUES);

        return $this; // for chaining function
    }

    // any operator
    public function any (string $column , string $subQuery , string $operator = '=') {
        $this->conditions[] = "$column $operator ANY ($subQuery)";

        return $this;
    }

    // all operator
    public function all (string $column , string $subQuery , string $operator = '=') {
        $this->conditions[] = "$column $operator ALL ($subQuery)";

        return $this;
    }

// =========================
// SECTION RUN QUERYS

    public const RUN_SQL = 1; // for run_sql ()
    public const RUN_CREATE_TABLE = 2; // for run_create_table
    public const RUN_PREPARE_EXECUTE = 3; // for run_pe ()

    public function run (string $run = self::RUN_SQL , int $outputType = self::OP_NO_CHANGE , bool $echoQuery = false) {
        switch ($run) {
            case self::RUN_SQL:
                return $this->run_sql ($outputType , $echoQuery);

            case self::RUN_CREATE_TABLE:
                $this->run_create_table ();
                break;

            case self::RUN_PREPARE_EXECUTE:
                return $this->run_pe ();
        }
    }

    // run create_table query
    private function run_create_table (): bool {
        try {
            $this->result = $this->prepare ($this->create_table . ')');
            $this->result->execute ();

            $this->create_table = ''; // reset create table query

            return true;
        } catch (PDOException $e) {
            $this->create_table = ''; // reset create table query

            return false;
        }
    }

    // run sql array
    private function run_sql (int $outputType = self::OP_NO_CHANGE , bool $echoQuery = false) {
        // replace last comma with space
        if (!empty ($this->sql['func'])) {
            $this->sql['func'] = substr ($this->sql['func'] , 0 , strlen ($this->sql['func']) - 1) . ' ';

            // add comma in first column string
            if (!empty ($this->sql['column'])) {
                $this->sql['column'] = ',' . $this->sql['column'];
            }
        }

        // add comma to case statement
        if ($this->sql['case'] && ($this->sql['func'] || $this->sql['column'])) {
            $this->sql['case'] = ',' . $this->sql['case'];
        }

        $this->cc (); // conditions concatenation
        // baraye zamani ast ke as where estefadeh nemikonim ama as operators manande (IN) estefadeh mikonim
        if (!empty ($this->sql['where']) && strpos ($this->sql['where'] , 'WHERE') !== 0) {
            $this->sql['where'] = 'WHERE ' . $this->sql['where'];
        }

        // finally sql
        $sql = $this->initialize_sql ($echoQuery);

        // result
        switch ($outputType) {
            case self::OP_QUERY:
                $this->reset_sql ();

                return $sql;
            default:
                $this->reset_sql ();

                // actual run
                $this->finish ($sql , $outputType);
                break;
        }
    }

    // for run simple query
    private $query = '';

    # Prepare, Execute - for simple query

    private function run_pe () {
        try {
            $result = $this->prepare ($this->query);
            $result->execute ();

            return $this;
        } catch (PDOException $e) {
            return false;
        }
    }

    // initialize query string for run()
    private function initialize_sql (bool $echoQuery = false) {
        $sql = '';

        // concatenation sql components
        if (strpos ($this->sql['from'] , ',') === false) {
            $sql = $this->sql_concat ();
        } else {
            $sql = $this->prepare_join ();
        }

        // echo finally query string
        if ($echoQuery) {
            echo "<p style='margin: 20px 0; font-size: 20px; padding: 10px; border: 4px double #00f; font-weight: bold;'>$sql</p>";
        }

        return $sql; // finally sql
    }

    // sql concatenation
    private function sql_concat (): string {
        $sql = '';

        foreach ($this->sql as $v) {
            $sql .= $v;
        }

        return $sql;
    }

    private function prepare_join (): string {
        $tbl = explode (',' , $this->sql['from']);

        $join = reset ($tbl);

        for ($i = 0; $i < count ($tbl) - 1; $i++) {
            $tbl2 = $tbl[$i + 1]; // gereftane table be joz avali

            $on = '';
            if ($this->sql['on'] [$i]) {
                $on = "ON " . $this->sql['on'] [$i];
            }

            // concat join component
            $join .= " $this->joinType JOIN $tbl2 $on ";
        }

        $this->sql['from'] = $join;
        $this->sql['on'] = null;

        return $this->sql_concat ();
    }

    // actual run - end work in the run $sql array
    private function finish (string $sql , int $outputType = self::OP_NO_CHANGE) {
        try {
            $this->result = $this->prepare ($sql);

            $this->binding ();

            $this->result->execute ();

            return $this->output ($outputType);
        } catch (PDOException $e) {
            return false;
        }
    }

    // reset sql & conditions array
    private function reset_sql () {
        // empty sql array
        foreach ($this->sql as $k => $v) {
            $this->sql[$k] = null;
        }

        // remove conditions
        for ($p = 0; $p < count ($this->conditions); $p++) {
            unset ($this->conditions[$p]);
        }
    }

// =========================
// SECTION ADD RANDOM DATA
    // $data: an array of column and data that the data can include data type, alphabet type and length.
    // data type: s = string / i = int / t = time / f = fixedText / e = email / r = randomNumber
    // alphabet type: l = lower case / u = upper case / f = farsi lang
    // length: 1-100 -> i: that is, from one to onehundred. r: that is, between one to onehundred
    // Example
    // s,u,2    s,l,2-5     e,f,4-8     t       f,iran      i,1-10      i,4     r,0-100
    # Add Random Data
    public function ard (string $tblName , array $data , int $count = 1) {
        $components = [];

        // convert each $data to one array
        foreach ($data as $key => $value) {
            $components[$key] = explode (',' , $value);
        }

        $records = []; // for multiple insert into
        // detect data type for generate content
        for ($i = 0; $i < $count; $i++) {
            $records[] = $this->ard_split ($data , $components);
        }

        $this->add_row ($tblName , $records);
    }

    // detect data type for generate content
    private function ard_split (array $mainData , array $components): array {
        foreach ($components as $k => $v) {
            switch ($v[0]) {
                case 's':
                    $mainData[$k] = $this->rand_str ($v[1] , $v[2]);
                    break;

                case 'e':
                    $mainData[$k] = $this->rand_str ($v[1] , $v[2] , '@email.ir');
                    break;

                case 'i':
                    $mainData[$k] = $this->rand_int ($v[1]);
                    break;

                case 't':
                    $mainData[$k] = date ('Y/m/d H:i:s');
                    break;

                case 'f':
                    $mainData[$k] = $v[1];
                    break;

                case 'r':
                    $mainData[$k] = $this->random_number ($v[1]);
                    break;
            }
        }

        return $mainData;
    }

    public function rand_str (string $case , string $length , string $salt = null) {
        if ($case == 'u' || $case == 'l') {
            return $this->rand_english ($case , $length , $salt);
        } else if ($case == 'f') {
            return $this->rand_farsi ($length , $salt);
        }
    }

    // set length string (useful for rand_str functiion and so on)
    private function set_length (string $array): string {
        if (count ($e = explode ('-' , $array)) == 2) {
            return rand ($e[0] , $e[1]);
        } else {
            return $e[0];
        }
    }

    private function rand_int (string $length): string {
        $str = '';

        $maxLength = $this->set_length ($length);

        for ($p = 0; $p < $maxLength; $p++) {
            $str .= rand (0 , 9);
        }

        return $str;
    }

    public function random_number (string $string): string {
        if (count ($e = explode ('-' , $string)) == 2) {
            return rand ($e[0] , $e[1]);
        }
    }

    // useful for generate random string
    private $en = 'abcdefghijklmnopqrstuvwxyz';
    private $fa = ['ا' , 'ب' , 'پ' , 'ت' , 'ث' , 'ج' , 'چ' , 'ح' , 'خ' , 'د' , 'ذ' , 'ر' , 'ز' , 'ژ' , 'س' , 'ش' , 'ص' , 'ض' , 'ط' , 'ظ' , 'ع' , 'غ' , 'ف' , 'ق' , 'ک' , 'گ' , 'ل' , 'م' , 'ن' , 'و' , 'ه' , 'ی'];

    // generate random farsi text
    private function rand_farsi (string $length , string $salt = null): string {
        $str = '';

        $maxLength = $this->set_length ($length);

        for ($k = 0; $k < $maxLength; $k++) {
            $str .= $this->fa[rand (0 , 31)];
        }

        return $str . $salt;
    }

    // generate random english text
    private function rand_english (string $case , string $length , string $salt = null): string {
        $str = '';

        $maxLength = $this->set_length ($length);

        for ($k = 0; $k < $maxLength; $k++) {
            $str .= $this->en[rand (0 , 25)];
        }

        // return upper or lower string
        switch ($case) {
            case 'u': return strtoupper ($str . $salt);
            case 'l': return strtolower ($str . $salt);
        }
    }

// =========================
// del
//    public function get_row (string $tblName, string $columns='*', array $condition=null) {
//        try {
//            $sql = "SELECT $columns FROM $tblName";
//
//            $this->result = $this->prepare ($sql);
//            $this->result->execute ();
//
//            $this->output (self::OP_HTML_TABLE);
//        } catch (PDOException $e) {
//            return false;
//        }
//    }
// =========================

    public function empty_table (string $tblName): bool {
        try {
            $sql = "TRUNCATE TABLE $tblName";

            $this->result = $this->prepare ($sql);
            $this->result->execute ();

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

// =========================

    public const OP_PRINT_R = 1; // return result by print_r function
    public const OP_VAR_DUMP = 2; // return result by var_dump function
    public const OP_HTML_TABLE = 3; // return result by HTML tag
    public const OP_NO_CHANGE = 4; // return no changeable result
    private const OP_STRING = 5; // return string value (one component)
    public const OP_QUERY = 6;

    public function output (int $outputType = self::OP_NO_CHANGE) {
        switch ($outputType) {
            case self::OP_PRINT_R:
                return $this->op_print_r ();

            case self::OP_VAR_DUMP:
                return $this->op_var_dump ();

            case self::OP_NO_CHANGE:
                return $this->op_no_change ();

            case self::OP_HTML_TABLE:
                return $this->op_html_table ();

            case self::OP_STRING:
                return $this->op_string ();
        }
    }

    private function op_print_r (): bool {
        $this->result->setFetchMode (self::FETCH_BOTH);

        if ($R = $this->result->fetchAll ()) {
            echo'<pre style="font-size:15px;"><b>';
            print_r ($R);
            echo'</b></pre>';

            return true;
        } else {
            echo '<br/> [ No Result ] <br/>';

            return false;
        }
    }

    private function op_var_dump (): bool {
        $this->result->setFetchMode (self::FETCH_BOTH);

        if ($R = $this->result->fetchAll ()) {
            echo'<pre style="font-size:15px;"><b>';
            var_dump ($R);
            echo'</b></pre>';

            return true;
        } else {
            echo '<br/> [ No Result ] <br/>';

            return false;
        }
    }

    private function op_no_change () {
        $this->result->setFetchMode (self::FETCH_BOTH);

        if ($R = $this->result->fetchAll ()) {
            return $R;
        }

        return false;
    }

    private function op_html_table (): bool {
        $this->result->setFetchMode (PDO::FETCH_ASSOC);
        $r = $this->result->fetchAll ();

        // if no result then end function
        if (empty ($r)) {
            return false;
        }

        echo '<table style="border: 1px solid #000; width: 100%; font-size: 20px;" cellspacing="0px" cellpadding="5px">';

        echo '<thead>';
        foreach ($r[0] as $key => $value) {
            echo "<th style='text-align: center; border: 1px solid #000; margin:0;'>$key</th>";
        }
        echo '</thead>';

        foreach ($r as $item) {
            echo '<tr>';
            foreach ($item as $k => $v) {
                echo "<td style='border: 1px solid #000; text-align: center;'>$v</td>";
            }
            echo '</tr>';
        }

        echo '</table>';

        return true;
    }

    private function op_string () {
        if ($row = $this->result->fetch ()) {
            return $row[0];
        } else {
            return false;
        }
    }

}

// =========================

// EDAMEHYE RAH
// + ard() -> emkane save be surate hash
// + exists operator
// + self join
// + insert into select statement
// + index
