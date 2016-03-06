<?php

$args = readArgv();

if ($args[0] === 'dbcleaner.php') {
  array_shift($args);
} //if

$database_pattern_name = $args[0];
$database_guest_name = $args[1];

$link_guest = connect($database_guest_name);
$link_pattern = connect($database_pattern_name, true);

checkDbStrict($link_guest);

$frameworks = array(
  'activity_logs',
  'announcements',
  'assignees',
  'attachments',
  'authentication',
  'avatar',
  'categories',
  'comments',
  'complete',
  'custom_fields',
  'download',
  'email',
  'environment',
  'favorites',
  'file_uploader',
  'globalization',
  'history',
  'homescreens',
  'labels',
  'modules',
  'notifications',
  'payments',
  'preview',
  'reminders',
  'reports',
  'search',
  'subscriptions',
  'subtasks',
  'text_compare',
  'visual_editor',
  // not in vanilla but still ac
  'password_policy'
);

$guest_prefix = getDatabasePrefix($database_guest_name, $link_guest);
if ($guest_prefix !== 'acx_') {
  setDatabasePrefix($guest_prefix, 'acx_', $link_guest);
}

uninstallCustomModules($link_guest, $link_pattern, $frameworks);

deleteNonVanillaTables($link_guest, $link_pattern);

deleteNonVanillaTableFields($link_guest, $database_guest_name, $link_pattern, $database_pattern_name);

emptyIndexes($link_guest, $database_guest_name);

//==============================================================================================================================
// Function for making sure that we are working in MySQL strict mode
//==============================================================================================================================

function checkDbStrict($link_guest) {

  print <<<END

//==============================================================================================================================
// Putting MySQL in strict mode
//==============================================================================================================================

END;

  $strict_mode_query = mysql_query("SET sql_mode = 'STRICT_ALL_TABLES'", $link_guest);
  if (!$strict_mode_query) {
    print("Could not put MySQL in strict mode: " . mysql_error($link_guest)); die();
  } else {
    print "Successfully put MySQL in strict mode! \n";
  } // if
} //checkDbStrict


//==============================================================================================================================
// Function for renaming prefixes
//==============================================================================================================================
function getDatabasePrefix($database_guest_name, $link_guest) {
  print <<<END

//==============================================================================================================================
// Renaming database prefix
//==============================================================================================================================

END;

  $query = mysql_query("SHOW TABLES WHERE Tables_in_$database_guest_name LIKE '%update_history'", $link_guest);
  $table_name = first(mysql_fetch_row($query));

  $prefix_ending = strpos($table_name, 'update_history');
  return substr($table_name, 0, $prefix_ending);
} // getDatabasePrefix

function setDatabasePrefix($current_prefix, $new_prefix, $link_guest) {
  $query = mysql_query("SHOW TABLES", $link_guest);
  while ($row = mysql_fetch_row($query)) {
    $current_table_name = $row[0];
    if (strpos($current_table_name, $current_prefix) !== false) {
      $new_table_name = str_replace($current_prefix, $new_prefix, $current_table_name);
      $rename_query = mysql_query("RENAME TABLE $current_table_name TO $new_table_name", $link_guest);
      if (!$rename_query) {
        print("Could not rename table $current_table_name: " . mysql_error($link_guest)); die();
      } else {
        print "Table $current_table_name successfully renamed to $new_table_name! \n";
      } // if
    } // if
  } // while
} // setDatabasePrefix

//==============================================================================================================================
// Function for uninstalling third party modules
//==============================================================================================================================
function uninstallCustomModules($link_guest, $link_pattern, $frameworks) {
  print <<<END

//==============================================================================================================================
// Removing custom modules data
//==============================================================================================================================

END;
  $modules_table = 'acx_modules';
  $project_object_table = 'acx_project_objects';
  $config_table = 'acx_config_options';
  $config_values_table = 'acx_config_option_values';

  // get all the vanilla modules
  $vanilla_modules = array();
  $query = mysql_query("SELECT name FROM $modules_table", $link_pattern);

  while ($row = mysql_fetch_assoc($query)) {
    $vanilla_modules[] = $row["name"];
  } //while

  // get all the data from project_object table and delete if not from vanilla module
  $query = mysql_query("SELECT DISTINCT module FROM $project_object_table", $link_guest);

  while ($row = mysql_fetch_assoc($query)) {
    $guest_module_name = $row["module"];
    if (!in_array($guest_module_name, $vanilla_modules)) {
      $delete_query = mysql_query("DELETE FROM $project_object_table WHERE module = '$guest_module_name'", $link_guest);
      if (!$delete_query) {
        print("Could not remove data for $guest_module_name module from $project_object_table table : " . mysql_error($link_guest));die();
      } else {
        print mysql_affected_rows($link_guest) . " data for $guest_module_name from $project_object_table table successfully removed! \n";
      } // if
    } // if
  } //while

  // get all the data from config_options table and delete if not from vanilla module
  $query = mysql_query("SELECT DISTINCT module FROM $config_table", $link_guest);

  while ($row = mysql_fetch_assoc($query)) {
    $guest_module_name = $row["module"];
    if (!in_array($guest_module_name, $vanilla_modules) && !in_array($guest_module_name, $frameworks)) {
      $delete_query = mysql_query("DELETE FROM $config_table WHERE module = '$guest_module_name'", $link_guest);
      if (!$delete_query) {
        print("Could not remove data for $guest_module_name module from $project_object_table table : " . mysql_error($link_guest));die();
      } else {
        print mysql_affected_rows($link_guest) . " data for $guest_module_name from $config_table table successfully removed! \n";
      } // if
    } // if
  } //while

  // get all the data from config_option_values table and delete if not from vanilla module
  $query = mysql_query("SELECT parent_type FROM $config_values_table", $link_guest);
  $vanilla_parent_types = array('User', 'Company', 'Project');

  while ($row = mysql_fetch_assoc($query)) {
    $guest_parent_type = $row["parent_type"];
    if (!in_array($guest_parent_type, $vanilla_parent_types)) {
      $delete_query = mysql_query("DELETE FROM $config_values_table WHERE parent_type = '$guest_parent_type'", $link_guest);
      if (!$delete_query) {
        print("Could not remove data for $guest_parent_type parent_type from $config_values_table table : " . mysql_error($link_guest));die();
      } else {
        print mysql_affected_rows($link_guest) . " data for $guest_parent_type parent_type from $config_values_table table successfully removed! \n";
      } // if
    } // if
  } // while

  // get all the data from config_options table and delete if not from vanilla module
  $query = mysql_query("SELECT DISTINCT module FROM $config_table", $link_guest);

  while ($row = mysql_fetch_assoc($query)) {
    $guest_module_name = $row["module"];
    if (!in_array($guest_module_name, $vanilla_modules) && !in_array($guest_module_name, $frameworks)) {
      $delete_query = mysql_query("DELETE FROM $config_table WHERE module = '$guest_module_name'", $link_guest);
      if (!$delete_query) {
        print("Could not remove data for $guest_module_name module from $config_table table : " . mysql_error($link_guest));die();
      } else {
        print mysql_affected_rows($link_guest) . " data for $guest_module_name from $config_table table successfully removed! \n";
      } // if
    } // if
  } //while

  // get all the data from modules table and delete if not from vanilla module
  $query = mysql_query("SELECT name FROM $modules_table", $link_guest);

  while ($row = mysql_fetch_assoc($query)) {
    $guest_module_name = $row["name"];
    if (!in_array($guest_module_name, $vanilla_modules)) {
      $delete_query = mysql_query("DELETE FROM $modules_table WHERE name = '$guest_module_name'", $link_guest);
      if (!$delete_query) {
        print("Could not remove data for $guest_module_name module from $modules_table table : " . mysql_error($link_guest));die();
      } else {
        print"$guest_module_name from $modules_table table successfully removed! \n";
      } // if
    } // if
  }


} //removeCustomModules

//==============================================================================================================================
// Function for deleting none-vanilla tables
//==============================================================================================================================

function deleteNonVanillaTables($link_guest, $link_pattern) {
  print <<<END

//==============================================================================================================================
// Deleting non-activecollab tables
//==============================================================================================================================

END;

  $query = mysql_query('SHOW TABLES', $link_pattern);

  $pattern_tables = array();
  while ($row = mysql_fetch_row($query)) {
    $pattern_tables[] = $row[0];
  } //while

  $query = mysql_query('SHOW TABLES', $link_guest);
  while ($row = mysql_fetch_row($query)) {
    $table_name = $row[0];
    if (!in_array($table_name, $pattern_tables)) {
      $delete_query = mysql_query("DROP TABLE $table_name", $link_guest);
      if (!$delete_query) {
        print("Could not delete table $table_name: " . mysql_error($link_guest));die();
      } else {
        print "Table $table_name successfully deleted! \n";
      } // if
    } // if
  } // while
} // deleteNonVanillaTables

//==============================================================================================================================
// Function for deleting none-vanilla table fields
//==============================================================================================================================

function deleteNonVanillaTableFields($link_guest, $database_guest_name, $link_pattern, $database_pattern_name) {
  print <<<END

//==============================================================================================================================
// Deleting non-activecollab fields and showing differences in the field types
//==============================================================================================================================

END;

  $tables_query = mysql_query('SHOW TABLES', $link_pattern);
  while ($table_row = mysql_fetch_row($tables_query)) {
    $table_name = $table_row[0];

    $pattern_fields = array();
    $pattern_fields_query = mysql_query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$database_pattern_name' AND TABLE_NAME = '$table_name'", $link_pattern);
    while ($fields_row = mysql_fetch_row($pattern_fields_query)) {
      $pattern_fields[] = $fields_row[0];
    } //while



    $guest_fields_query = mysql_query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$database_guest_name' AND TABLE_NAME = '$table_name'", $link_guest);
    if (!mysql_num_rows($guest_fields_query)) {
      //if the guest does not have that table we skip this iteration
      continue;
    } //if

    while ($fields_row = mysql_fetch_row($guest_fields_query)) {
      $guest_field = $fields_row[0];
      $key = array_search($guest_field, $pattern_fields, true);
      if ($key === false) {
        $delete_query = mysql_query("ALTER TABLE $table_name DROP $guest_field", $link_guest);
        if (!$delete_query) {
          print("Could not delete field '$guest_field' from $table_name table : " . mysql_error($link_guest));die();
        } else {
          print "Field '$guest_field' from '$table_name' table successfully removed! \n";
        } // if
      } else {
        unset($pattern_fields[$key]);
        // check field equality

        $pattern_field_details = mysql_fetch_assoc(mysql_query("SELECT COLUMN_NAME, COLUMN_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE, COLUMN_KEY, EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$database_pattern_name' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = '$guest_field'", $link_pattern));
        $guest_field_details = mysql_fetch_assoc(mysql_query("SELECT COLUMN_NAME, COLUMN_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE, COLUMN_KEY, EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$database_guest_name' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = '$guest_field'", $link_guest));

        if ($pattern_field_details !== $guest_field_details) {
          print "\nDifferences in $table_name table, $guest_field field: \n";
          $i = 0;
          foreach ($pattern_field_details as $detail => $value) {
            if ($value !== $guest_field_details[$detail]) {
              $i += 1;
              print "$i. Parameter: $detail; Pattern: $value; Guest: {$guest_field_details[$detail]} \n";
            } //if
          } //foreach
        } //if
      } //if
    } //while

    $pattern_fields = array_values($pattern_fields);
    if (count($pattern_fields) > 0) {
      foreach($pattern_fields as $pattern_field) {
        print "\nMissing field '$pattern_field' from '$table_name' table! \n";
      } //if
    } //if
  } //while
} // deleteNonVanillaTableFields



//==============================================================================================================================
// Empty indexes
//==============================================================================================================================

function emptyIndexes($link_guest, $database_guest_name) {
  print <<<END

//==============================================================================================================================
// Emptying indexes
//==============================================================================================================================

END;
  $search_index_tables_query = mysql_query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$database_guest_name' AND TABLE_NAME LIKE 'acx\_search\_index\_for%'", $link_guest);
  while ($table_name_row = mysql_fetch_row($search_index_tables_query)) {
    $table_name = $table_name_row[0];
    $delete_query = mysql_query("DELETE FROM $table_name", $link_guest);
    if (!$delete_query) {
      print("Could not remove data from $table_name table : " . mysql_error($link_guest));die();
    } else {
      print mysql_affected_rows($link_guest) . " rows from $table_name table successfully removed! \n";
    } // if
  } //while
} //emptyIndexes



//==============================================================================================================================
// Utility functions
//==============================================================================================================================

function connect($database, $new_link = false) {
  if ($new_link) {
    $link = mysql_connect('127.0.0.1', 'root', '', true);
  } else {
    $link = mysql_connect('127.0.0.1', 'root', '');
  }

  if(is_resource($link)) {
    if(mysql_select_db($database, $link)) {
      return $link;
    } // if
  } //if

  return false;
} // connect


function pre_var_dump($var) {
  print "<pre style=\"text-align: left\">\n";

  var_dump($var);

  print "</pre>\n";
} // pre_var_dump

/**
 * Returns first element of an array
 *
 * If $key is true first key will be returned, value otherwise.
 *
 * @param array $arr
 * @param boolean $key
 * @return mixed
 */
function first($arr, $key = false) {
  foreach($arr as $k => $v) {
    return $key ? $k : $v;
  } // foreach
} // first

/**
 * Safely read the $argv PHP array across different PHP configurations
 *
 * Will take care on register_globals and register_argc_argv ini directives
 *
 * @return mixed the $argv
 */
function readArgv() {
  global $argv;
  if(!is_array($argv)) {
    if(!@is_array($_SERVER['argv'])) {
      if(!@is_array($GLOBALS['HTTP_SERVER_VARS']['argv'])) {
        die('CLI error');
      } // if
      return $GLOBALS['HTTP_SERVER_VARS']['argv'];
    } // if
    return $_SERVER['argv'];
  } // if
  return $argv;
} // readArgv