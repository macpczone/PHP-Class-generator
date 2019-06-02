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
                $class = explode("_", $class);
                $newClass = "";
                foreach ($class as $c) {
                    $newClass .= ucwords($c);
                }
                $class = $newClass . "DB";

                $content = "<?php\n\n";
                $content .= "namespace App\Libraries;\n\n";
                $content .= "use Illuminate\Support\Facades\DB;\n";
                $content .= "use Illuminate\Support\Facades\Log;\n\n";

                /***********************************************************************
                 * CLASS
                 ************************************************************************/
        
                $content .= "class $class {\n\n";


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
                    $content .= "\tprivate $$str_column = null;\n";
                    $list_columns_var_val[] = "$$str_column = null";
                    $list_columns_var[] = "$$str_column";
                    $list_columns[] = $str_column;
                }

                $content .= "\n";
   
                foreach ($columns as $column) {
                    // setters
                    $str_column = str_replace($this->str_replace_column, '', $column);
                    $content .= "\tpublic function set_$str_column($$str_column) {\n";
                    $content .= "\t\t\$this->$str_column = $$str_column;\n";
                    $content .= "\t}\n";
                    // getters
                    $str_column = str_replace($this->str_replace_column, '', $column);
                    $content .= "\tpublic function get_$str_column() {\n";
                    $content .= "\t\treturn \$this->$str_column;\n";
                    $content .= "\t}\n\n";
                }
                
                // lookup
                $content .= "\tpublic static function lookup(\$where = '1', \$params = array()) {\n";
                $content .= "\t\t\$sql = \"SELECT * FROM $table WHERE \$where\";\n\n";
                
                // try catch
                $content .= "\t\ttry{\n";
                $content .= "\t\t\t \$dbh = DB::getPdo(); \n";
                $content .= "\t\t\t \$sth = \$dbh->prepare(\$sql); \n";
                $content .= "\t\t\t \$sth->execute(\$params); \n";
                $content .= "\t\t} catch(\PDOException \$e) {\n";
                $content .= "\t\t\tLog::info(\$sql); \n";
                $content .= "\t\t\tLog::info(\"Failed to execute query\"); \n";
                $content .= "\t\t\treturn false; \n";
                $content .= "\t\t}\n\n";
                
                $content .= "\t\treturn \$sth->fetchAll(\PDO::FETCH_CLASS, get_class());\n";
                $content .= "\t}\n\n";

                // populate
                $content .= "\tpublic static function populate(". implode(', ', $list_columns_var_val) .") {\n"; 
                $content .= "\t\t\$classname = get_class();\n";
                $content .= "\t\t\$item = new \$classname();\n";
                foreach ($columns as $column) {
                    $str_column = str_replace($this->str_replace_column, '', $column);
                    $content .= "\t\t\$item->set_$str_column($$str_column);\n";
                }
                $content .= "\t\treturn \$item;\n";
                $content .= "\t}\n\n";

                // write
                $content .= "\tpublic function write() {\n"; 
                $content .= "\t\t\$params = array(\n";
                foreach ($columns as $column) {
                    $str_column = str_replace($this->str_replace_column, '', $column);
                    $content .= "\t\t\t\$this->get_$str_column(),\n";
                    $list_as_equals[] = $str_column . ' = ?';
                    $list_as_question_marks[] = '?';
                }
                $content .= "\t\t);\n\n";
                
                // id does not have value yet on insert
                $list_as_question_marks[0] = "null";

                $content .= "\t\tif (\$this->get_$list_columns[0]() == null) {\n";
                $content .= "\t\t\tunset(\$params[0]);\n";
                $content .= "\t\t\t\$params = array_values(\$params);\n";
                $content .= "\t\t\t\$sql = \"INSERT INTO $table(" . implode(', ', $list_columns) .") VALUES (". implode(', ', $list_as_question_marks) . ")\";\n";
                $content .= "\t\t} else {\n";
                $content .= "\t\t\t\$sql = \"UPDATE $table SET " . implode(', ', $list_as_equals) . " WHERE " . $list_columns[0] . " = ?\";\n"; 
                $content .= "\t\t\t\$params[] = \$this->" . $list_columns[0] .";\n";
                $content .= "\t\t}\n\n";

                $content .= "\t\ttry{\n";
                $content .= "\t\t\t\$dbh = DB::getPdo(); \n";
                $content .= "\t\t\t\$stmt = \$dbh->prepare(\$sql);\n";
                $content .= "\t\t\t\$stmt->execute(\$params);\n";
                $content .= "\t\t} catch(\PDOException \$e) {\n";
                $content .= "\t\t\tLog::info(\$sql); \n";
                $content .= "\t\t\tLog::info(\"Failed to execute query\"); \n";
                $content .= "\t\t\treturn false; \n";
                $content .= "\t\t}\n";
                $content .= "\t\treturn true; \n";
                
                $content .= "\t}\n\n";

                // delete
                $content .= "\tpublic function delete() {\n";
                $content .= "\t\t\$sql = \"DELETE FROM $table WHERE $list_columns[0] = ?\";\n";

                $content .= "\t\ttry{\n";
                $content .= "\t\t\t\$dbh = DB::getPdo(); \n";
                $content .= "\t\t\t\$stmt = \$dbh->prepare(\$sql);\n";
                $content .= "\t\t\t\$stmt->execute(array(\$this->" . $list_columns[0] ."));\n";
                $content .= "\t\t} catch(\PDOException \$e) {\n";
                $content .= "\t\t\t Log::info(\$sql); \n";
                $content .= "\t\t\t Log::info(\"Failed to execute query\"); \n";
                $content .= "\t\t\t return false; \n";
                $content .= "\t\t}\n";
                $content .= "\t\treturn true; \n";
                $content .= "\t}\n";
                
                $content .= '}';

                // Write file
                $this->createClassFile($class, $content);
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