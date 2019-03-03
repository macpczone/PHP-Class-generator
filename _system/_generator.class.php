<?php

/**********************************************************************
 * ClassGenerator.class.php
 **********************************************************************/

define('PERMISSION_EXCEPTION', 'Permission error : No permission to write on ' . CLASSGENERATOR_DIR . '.');
define('SERVER_EXCEPTION', 'Host error : Enter a valid host.');
define('BASE_EXCEPTION', 'Database error : Enter a valid database.');
define('AUTH_EXCEPTION', 'Authentication error : Enter a valid user name and password.');

class ClassGenerator
{

    private $exception;
    private $str_replace = array('-');
    private $str_replace_file = array();
    private $str_replace_column = array(' ', '-');
    private $skip_table = array();

    public function __construct()
    {
        $this->generateClasses($this->getTables());
    }

    private function generateClasses($tables)
    {
        foreach ($tables as $table => $table_type) {
            if (!in_array($table, $this->skip_table)) {
                $class = str_replace($this->str_replace, '', $table);
                $class = preg_replace('/[0-9]+/', '', $class);
                if ($table == 'produit') {
                    $this->str_replace_column = array(' ', '-');
                } else {
                    $this->str_replace_column = array(' ', 'fld_', '-');
                }
                $content = '<?php' . NL . NL;

                /***********************************************************************
                 * CLASS
                 ************************************************************************/
                $type = ($table_type == 'BASE TABLE') ? 'Table' : 'View';
                $prefixe = ($table_type == 'BASE TABLE') ? '' : 'V_';
                $content .= 'class ' . ucwords($class) . ' {' . NL . NL;


                /***********************************************************************
                 * VARIABLES
                 ************************************************************************/
                $list_columns = array();
                $list_columns_var = array();
                $list_columns_var_val = array();
                $list_as_question_marks = array();
                $list_as_equals = array();
                $columns = $this->getColumns($table);
                $columns_info = $this->getColumnsInfo($table);
                $pKeys = $this->getPrimaryKeys($table);

                $primary_key = '';
                $columns_name = '';

                foreach ($columns as $column) {
                    $str_column = str_replace($this->str_replace_column, '', $column);
                    $content .= TAB . 'private $' . $str_column . ' = null;' . NL;
                    $list_columns_var_val[] = "$". $str_column . " = null";
                    $list_columns_var[] = "$". $str_column;
                    $list_columns[] = $str_column;
                }

                $content .= NL;
   
                foreach ($columns as $column) {
                    // setters
                    $str_column = str_replace($this->str_replace_column, '', $column);
                    $content .= TAB . 'public function set_' . $str_column . '($'. $str_column .') {' . NL;
                    $content .= TAB . TAB . '$this->' . $str_column . ' = $'. $str_column . ';' . NL;
                    $content .= TAB . '}' . NL;
                    // getters
                    $str_column = str_replace($this->str_replace_column, '', $column);
                    $content .= TAB . 'public function get_' . $str_column . '() {' . NL;
                    $content .= TAB . TAB . 'return $this->' . $str_column . ';' . NL;
                    $content .= TAB . '}' . NL . NL;
                }
                
                // lookup
                $content .= TAB . 'public static function lookup($dbh, $where = 1, $params = array()) {' . NL;
                $content .= TAB . TAB . '$sql = "SELECT '. implode(', ', $list_columns) .' FROM ' . $table . ' WHERE $where";' . NL;
                $content .= TAB . TAB . '$stmt = $dbh->prepare($sql);' . NL;
                $content .= TAB . TAB . '$stmt->execute($params);' . NL;
                $content .= TAB . TAB . '$results = $stmt->fetchAll(PDO::FETCH_CLASS, \''. $table .'\');' . NL;
                $content .= TAB . TAB . 'return $results;' . NL;
                $content .= TAB . '}' . NL . NL;

                // populate
                $content .= TAB . 'public static function populate('. implode(', ', $list_columns_var_val) .') {' . NL; 
                $content .= TAB . TAB . '$item = new ' . $table . '();' . NL;
                foreach ($columns as $column) {
                    $str_column = str_replace($this->str_replace_column, '', $column);
                    $content .= TAB . TAB . '$item->set_' . $str_column . '($'. $str_column .');' . NL;
                }
                $content .= TAB . TAB . 'return $item;' . NL;
                $content .= TAB . '}' . NL . NL;

                // write
                $content .= TAB . 'public function write($dbh) {' . NL; 
                $content .= TAB . TAB . '$params = array('. NL;
                foreach ($columns as $column) {
                    $str_column = str_replace($this->str_replace_column, '', $column);
                    $content .= TAB . TAB . TAB . '$this->get_' . $str_column . '(),' . NL;
                    $list_as_equals[] = $str_column . ' = ?';
                    $list_as_question_marks[] = '?';
                }
                $content .= TAB . TAB . ');'. NL;

                $content .= NL . TAB . TAB . 'if (!$this->' . $list_columns[0] . ') {';
                $content .= NL . TAB . TAB . TAB .'$sql = "INSERT INTO '. $table . ' ('. implode(', ', $list_columns) .') 
                    VALUES ('. implode(', ', $list_as_question_marks) .')";' . NL;
                $content .= TAB . TAB . '} else {' . NL;
                $content .= TAB . TAB . TAB . '$sql = "UPDATE '. $table . ' 
                    SET ' . implode(', ', $list_as_equals) . ' 
                    WHERE ' . $list_columns[0] . ' = ?";' . NL; 
                $content .= TAB . TAB . TAB . '$params[] = $this->' . $list_columns[0] .';'. NL;
                $content .= TAB . TAB . '}' . NL . NL;

                $content .= TAB . TAB . '$stmt = $dbh->prepare($sql);' . NL;
                $content .= TAB . TAB . '$stmt->execute($params);' . NL;
                $content .= TAB . '}' . NL . NL;

                // delete
                $content .= TAB . 'public function delete($dbh) {' . NL; 
                $content .= TAB . TAB . '$params = array($this->' . $list_columns[0] .');'. NL;
                $content .= TAB . TAB . '$sql = "DELETE FROM '. $table .' WHERE ' . $list_columns[0] . ' = ?";' . NL;
                $content .= TAB . TAB . '$stmt = $dbh->prepare($sql);' . NL;
                $content .= TAB . TAB . '$stmt->execute($params);' . NL;
                $content .= TAB . '}' . NL;

                $content .= '}';

                // Write file
                $this->createClassFile($prefixe . str_replace($this->str_replace_file, '', $table), $content);
            }
        }
    }

    private function getColumns($table)
    {
        $result = Database::select('SHOW COLUMNS FROM `' . $table . '`');
        $columns = array();

        foreach ($result as $key => $column)
            $columns[$key] = $column['Field'];

        return $columns;
    }

    private function getColumnsInfo($table)
    {
        $result = Database::select('SHOW FULL COLUMNS FROM `' . $table . '`');
        $columns = array();

        foreach ($result as $key => $column) {
            $columns[$column['Field']]['Comment'] = $column['Comment'];
            $columns[$column['Field']]['Type'] = $column['Type'];
        }
        return $columns;
    }

    public function getPrimaryKeys($table)
    {
        $result = Database::select('SHOW COLUMNS FROM `' . $table . '`');
        $pKeys = array();

        foreach ($result as $key => $column) {
            if ($column['Key'] == 'PRI') {
                $pKeys[$key] = $column['Field'];
            }
        }

        return $pKeys;
    }

    private function createClassFile($file_to_save, $text_to_save)
    {
        $file = CLASSGENERATOR_DIR . ucwords($file_to_save) . '.php';
        chmod(CLASSGENERATOR_DIR, 0777);
        if (!file_exists($file))
            if (!touch($file))
                $this->exception = PERMISSION_EXCEPTION;
            else
                chmod($file, 0777);
        $fp = fopen($file, 'w');
        fwrite($fp, $text_to_save);
        fclose($fp);
    }

    private function getTables()
    {
        $result = Database::select('SHOW FULL TABLES');
        $tables = array();

        foreach ($result as $key => $table) {
            $tables[$table['Tables_in_' . dbdatabase]] = $table['Table_type'];
        }

        return $tables;
    }

    public function getException()
    {
        return $this->exception;
    }
}

?>