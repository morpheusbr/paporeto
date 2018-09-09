<?php

/*
 *    author:		Kyle Gadd
 *    documentation:	http://www.php-ease.com/classes/sqlite.html
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class SQLite {

  private $db;
  private $result;

  public function __construct ($database='', $version=3) {
    $version = ($version == 2) ? 2 : 3;
    $prefix = ($version == 2) ? 'sqlite2' : 'sqlite';
    if (empty($database)) {
      $path = ':memory:';
    } else {
      $path = DIR_DB.$database.'.db'.$version;
      if (!is_dir(dirname($path))) mkdir(dirname($path), 0755, true);
    }
    if (!($this->db = new PDO ($prefix . ':' . $path))) {
      $info = $this->db->errorInfo();
      trigger_error ("O Banco de Dados Sqlite ({$database}) não conectou: {$info[2]}");
      return false;
    }
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    if (current($this->pragma('foreign_keys')) != '') {
      $this->pragma('foreign_keys', 'ON');
    } else {
      trigger_error ("Cuidado! As chaves estrangeiras do SQLite não são suportadas.");
    }
    return true;
  }
  
  public function attach ($database, $alias) {
    return $this->exec('ATTACH DATABASE "' . $database . '" AS ' . $alias);
  }
  
  public function create ($table, $columns, $changes=array()) {
    $fields = array();
    foreach ($columns as $name => $type) $fields[] = $name . ' ' . $type;
    $query = 'CREATE TABLE "' . $table . '" (' . implode(", \n\t", $fields) . ')';
    // See http://www.sqlite.org/fileformat2.html - 2.5 Storage Of The SQL Database Schema
    $executed = $this->value("SELECT sql FROM sqlite_master WHERE type='table' AND tbl_name='{$table}'");
    if ($query == $executed) return false; // the table has already been created in it's requested state
    if ($executed) { // then this table is being altered in some way
      $this->alter($table, $columns, $changes);
      trigger_error("<pre>Just FYI, an SQLite table was changed:\n\nFrom: {$executed}\n\nTo: {$query}</pre>");
    } else {
      $this->exec($query); // We should only get here once
    }
    return true; // the table has been created (or altered)
  }
  
  public function index ($table, $suffix, $columns) {
    if (!is_array($columns)) $columns = array($columns);
    $index = $table . '_index_' . $suffix;
    $query = "CREATE INDEX {$index} ON {$table} (" . implode(', ', $columns) . ")";
    $executed = $this->value("SELECT sql FROM sqlite_master WHERE type='index' AND name='{$index}'");
    if ($query == $executed) return true; // the index has already been created
    if ($executed) {
      $this->query("DROP INDEX {$index}");
      trigger_error("<pre>Um índice SQLite foi alterado: \n\n de:
 {$executed}\n\npara: {$query}</pre>");
    }
    return $this->exec($query);
  }
  
  public function pragma ($name, $value='', $all=false) {
    if ($value == '') {
      $this->query("PRAGMA {$name}");
      $result = $this->fetch('assoc', 'all');
      if ($all !== false) return $result;
      if (isset($result[0])) return $result[0];
      return false;
    }
    return $this->exec("PRAGMA {$name} = {$value}");
  }
  
  public function query ($query, $values=array()) {
    if (!empty($values)) return $this->statement($query, $values, 'select');
    if (!($this->result = $this->db->query($query))) {
      $info = $this->db->errorInfo();
      trigger_error ("Consulta SQLite: {$query}<br /><br />Erro: {$info[2]}");
      return false;
    }
    return true;
  }
  
  public function exec ($query) {
    $rows = $this->db->exec($query);
    if ($rows === false) {
      $info = $this->db->errorInfo();
      trigger_error ("SQLite Exec: {$query}<br /><br />Erro: {$info[2]}");
    }
    return $rows;
  }
  
  public function statement ($query, $values, $type='') { // select, insert, update, delete
    $rows = array();
    $stmt = $this->db->prepare($query);
    if ($stmt === false) {
      $info = $this->db->errorInfo();
      trigger_error("SQLite falhou ao preparar instrução: {$query}<br /><br />Erro: {$info[2]}<pre>" . print_r($values, true) . '</pre>');
      return false;
    }
    if (is_array($values[0])) {
      $this->db->beginTransaction();
      foreach ($values as $data) {
        $data = array_map('trim', $data);
        if (!($success = $stmt->execute($data))) {
          $this->db->rollBack();
          break;
        } else {
          $rows[] = ($type == 'insert') ? $this->db->lastInsertId() : $stmt->rowCount(); // else 'update' || 'delete'
        }
      }
      if ($success) $this->db->commit();
    } else {
      $values = array_map('trim', $values);
      $success = $stmt->execute($values);
      $rows[] = ($type == 'insert') ? $this->db->lastInsertId() : $stmt->rowCount(); // else 'update' || 'delete'
    }
    if (!$success) {
      $info = $stmt->errorInfo();
      trigger_error ("Instrução SQLite: {$query}<br /><br />Erro: {$info[2]}" . '<pre>' . print_r($info, true) . '</pre>' . $stmt->errorCode());
    }
    if ($type == 'select') { // returns true or false, and sets $this->result
      $this->result = $stmt;
      $rows = true;
    }
    unset ($stmt);
    if (is_array($rows) && count($rows) == 1) $rows = current($rows);
    return ($success) ? $rows : false;
  }
  
  public function insert ($table, $array) {
    $multiple = (isset($array[0])) ? true : false;
    $columns = ($multiple) ? array_keys($array[0]) : array_keys($array);
    $params = array();
    foreach ($columns as $count) $params[] = '?';
    if ($multiple) {
      $values = array();
      foreach ($array as $data) $values[] = array_values($data);
    } else {
      $values = array_values($array);
    }
    return $this->statement('INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $params) . ')', $values, 'insert');
  }
  
  public function update ($table, $array, $column, $id, $add='') {
    $multiple = (isset($array[0]) && is_array($id)) ? true : false;
    $columns = ($multiple) ? array_keys($array[0]) : array_keys($array);
    foreach ($columns as $key => $value) {
      $columns[$key] = $value . ' = ?';
    }
    if ($multiple) {
      $values = array();
      foreach ($array as $set) {
        list(, $where) = each($id);
        $values[] = array_merge(array_values($set), array($where));
      }
    } else {
      $values = array_merge(array_values($array), array($id));
    }
    return $this->statement('UPDATE ' . $table . ' SET ' . implode(', ', $columns) . ' WHERE ' . $column . ' = ? ' . $add, $values, 'update');
  }
  
  public function delete ($table, $column, $id, $add='') {
    return $this->statement('DELETE FROM ' . $table . ' WHERE ' . $column . ' = ? ' . $add, (array) $id, 'delete');
  }
  
  public function fetch ($return='num', $all=false) {
    if (!is_object($this->result)) return false;
    $style = (!empty($return)) ? 'PDO::FETCH_' . str_replace('ROW', 'NUM', strtoupper($return)) : '';
    return ($all !== false) ? $this->result->fetchAll(constant($style)) : $this->result->fetch(constant($style));
  }
  
  public function value ($query, $values=array()) {
    $this->query($query, $values);
    $values = $this->fetch('row', 'all');
    return (!empty($values[0][0])) ? $values[0][0] : false;
  }
  
  public function rowid ($table, $unique, $value) {
    $this->query("SELECT rowid FROM {$table} WHERE {$unique} = ? LIMIT 1", array($value));
    list($id) = $this->fetch('row');
    return (!empty($id)) ? $id : false;
  }
  
  public function fts ($action, $table='', $fields=array(), $text='', $limit='') {
    switch ($action) {
      case 'create':
        $primary = array_shift($fields);
        $columns = array();
        $columns["{$table}_id"] = "INTEGER UNIQUE REFERENCES {$table}({$primary}) ON DELETE CASCADE";
        foreach ($fields as $field) $columns[$field] = 'TEXT NOT NULL DEFAULT ""';
        return $this->create("{$table}_fts", $columns);
        break;
      case 'upsert':
        if (isset($_SESSION['search'])) unset($_SESSION['search']);
        $id = array_shift($fields);
        foreach ($fields as $k => $v) $fields[$k] = $this->fts_string($v);
        $row = $this->update("{$table}_fts", $fields, "{$table}_id", $id);
        if (empty($row)) {
          $fields = array_merge(array("{$table}_id"=>$id), $fields);
          return (!$this->insert("{$table}_fts", $fields)) ? false : true;
        } else {
          return true;
        }
        break;
      case 'search':
        $fields = (is_array($fields)) ? implode(" || ' ' || ", $fields) : (string) $fields;
        $query = "SELECT {$table}_id, fulltext(?, {$fields}) AS match FROM {$table}_fts ORDER BY match DESC LIMIT 200";
        $limit = explode(',', preg_replace('/[^0-9,]/i', '', $limit));
        $offset = (isset($limit[0])) ? (int) $limit[0] : 0;
        if (isset($_SESSION['search']) && $_SESSION['search']['query'] == $query) {
          $length = (isset($limit[1])) ? (int) $limit[1] : count($_SESSION['search']['results']);
          return array_slice($_SESSION['search']['results'], $offset, $length);
        }
        $this->fts('register');
        $this->query($query, array($text));
        $ids = array();
        while (list($id, $match) = $this->fetch('row')) {
          $ids[] = $id;
        }
        $_SESSION['search']['query'] = $query;
        $_SESSION['search']['results'] = $ids;
        $length = (isset($limit[1])) ? (int) $limit[1] : count($ids);
        return array_slice($ids, $offset, $length);
        break;
      case 'register': // a private option, but help yourself
        $this->db->sqliteCreateFunction('fulltext', array(&$this, 'fulltext'), 2);
        return true;
        break;
    }
  }
  
  private function fts_string ($string) {
    $string = strtolower(str_replace(array("\r\n", "\r", "\n", "'s"), ' ', strip_tags(nl2br($string))));
    // $string = strtr($string, array_flip(get_html_translation_table(HTML_ENTITIES))); // decode named entities
    // $string = preg_replace('//e', 'chr(\\1)', $string); // decode numbered entities
    // $string = iconv("utf-8", "ascii//TRANSLIT", $string); // convert characters
    $string = preg_replace("/&#?[a-z0-9]{2,8};/i", '', $string); // remove html entities
    $string = preg_replace('/[^a-z0-9\s]/i', '', $string); // make alpha numeric
    $string = preg_replace('/\s(?=\s)/', '', $string); // remove extraneous spaces
    return trim($string);
  }
  
  private function fulltext ($search, $string) {
    $result = 0;
    $search = explode(' ', $this->fts_string($search));
    $string = explode(' ', $string); // should already by processed by fts_string
    $search_count = count($search);
    $string_count = count($string);
    if ($search_count == 0 || $string_count == 0) return $result;
    $haystack = ' ' . implode(' ', $string) . ' ';
    $needle = ' ';
    for ($i=0; $i<$search_count; $i++) {
      $needle .= $search[$i] . ' ';
      $result += (substr_count($haystack, $needle) * ($i + 1)) / $string_count;
    }
    return $result;
  }
  
  private function alter ($table, $columns, $changes=array()) { // used in $this->create()
    $this->query("SELECT * FROM {$table} LIMIT 1");
    $row = $this->fetch('assoc');
    $map = array();
    foreach ($changes as $old => $new) {
      if (isset($columns[$new]) && isset($row[$old])) $map[$old] = $new; // legitimate changes
    }
    foreach ($row as $key => $value) {
      if (isset($columns[$key]) && !isset($map[$key])) $map[$key] = $key; // old fields that match the new
    }
    $results = array();
    $this->pragma('foreign_keys', 'OFF');
    $this->db->beginTransaction();
    $copy = "{$table}_copy";
    $results[] = $this->create($copy, $columns);
    if (!empty($map)) {
      $results[] = $this->exec('INSERT INTO ' . $copy . ' (' . implode(', ', array_keys($map)) . ') SELECT ' . implode(', ', array_values($map)) . ' FROM ' . $table);
    }
    $results[] = $this->exec("DROP TABLE {$table}");
    $results[] = $this->exec("ALTER TABLE {$copy} RENAME TO {$table}");
    foreach ($results as $result) {
      if ($result === false) {
        $this->db->rollBack();
        break;
      }
    }
    if ($result !== false) $this->db->commit();
    $this->pragma('foreign_keys', 'ON');
    return ($result !== false) ? true : false;
  }
  
}

?>