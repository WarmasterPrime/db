# db
Allows a means to execute SQL queries using PHP arrays/objects.



<style>
.db-main{position:relative;left:50%;transform:translateX(-50%);width:calc(100% - 10px);height:auto;min-height:10px;padding:4px;}
h1{text-align:center;border-bottom:1px solid #000;text-transform:uppercase;font-weight:bolder;}
h2{position:relative;left:2.0em;text-align:left;font-weight:bold;border:1px solid #000;width:auto;display:inline-block;}
pre{display:block;font-family:monospace;border:1px solid #000;background-color:rgb(30,30,30);color:#FFF;padding:4px;}
var{color:rgb(200,150,50);}
value, val{color:rgb(100,150,200);}
int{color:rgb(255,200,50);}
method{color:rgb(100,200,50);}
str{color:rgb(200,50,255);}
comment{color:rgb(100,100,100);}
bool{color:rgb(50,200,150);}
int{color:rgb(100,100,200);}
</style>
<div class='db-main'>
	<h1>Setup</h1>
	<h2>ini file</h2>
	<pre>
<var>server</var>=<value>localhost</value>
<var>username</var>=<val>root</val>
<var>password</var>=
<var>port</var>=<int>3389</int>
<var>backup_dir</var>=</pre>

<h2>Linking Configuration File To System</h2>
<ol>
	<li>Copy absolute path of ini file.</li>
	<li>Paste path of ini file into class ini method parameter.</li>
</ol>
<pre>
db::<method>ini</method>(<var>[PATH]</var>);
</pre>
- This can be found within the <b><str>db.php</str></b> main file at the bottom.
<br>
<h1>API Documentation</h1>
<h2>Setup</h2>
<br>
The way to implement a database query object is by creating the following...
<pre>
<var>$db</var>=<str>"database_name"</str>;
<var>$tb</var>=<str>"database_table_name"</str>;
<var>$cmd</var>=<method>array</method>(
	<str>"action"</str>=><str>"[ACTION_NAME]"</str>,
	<str>"column"</str>,
	<str>"column"</str>,
	...
);
<comment>// The "WHERE" and "LIKE" clauses/cases are both similar in formatting.</comment>
<var>$where</var>=<method>array</method>(
	<str>"[COLUMN_NAME]"</str>=><str>"[COLUMN_VALUE]"</str>,
	<str>"[COLUMN_NAME]"</str>=><str>"[COLUMN_VALUE]"</str>,
	...
);
<comment>// Specifying the "[OR]" operator will find records matching the "ID" value OR the "NAME" value. Mutiple can be included.</comment>
<var>$like</var>=<method>array</method>(
	<str>"[or]id"</str>=><str>"0"</str>,
	<str>"[or]name"</str>=><str>"apples"</str>,
	...
);

<var>$args</var>=<method>array</method>(
	<str>"db"</str>=<var>$db</var>,
	<str>"tb"</str>=<var>$tb</var>,
	<str>"cmd"</str>=<var>$cmd</var>,
	<str>"where"</str>=<var>$where</var>
);
<var>$res</var>=db::<method>connect</method>(<var>$args</var>);
</pre>
<br>
- Depending on the specified action, you won't need to specify other properties such as the database name ("DB") or even table name...
<br><br>

<h2>Action Names:</h2>
<br>
<pre>
<str>select</str>:					Returns an array of values from the specified columns (Not to be confused with the "WHERE" or "LIKE" clause(s)).
<str>insert</str>:					Adds a new record consisting of data specified with the columns and their respective values (Not to be confused with the "WHERE" or "LIKE" clause(s)).
<str>update</str>:					Changes an existing record based on the respective column names and their values, matching a where clause.
<str>get_db</str>:					Returns an array consisting of all the databases on the current server.
<str>get_tb</str>:					Returns an array consisting of all database tables. Can be used with a where clause.
<str>get_type</str>:				Returns the data-type of the specified column names.
<str>delete</str>:					Removes either a record, database table, or database depending if there is a where clause specifying which records to delete or not.
<str>reset_id</str>:				Resets the unique ID counter that is already generated upon database creation.
<str>new_db</str>:					Creates a new database based on the specified name provided within the arguments array/object.
<str>new_tb</str>:					Creates a new database table based on the specified name provided within the arguments array/object.
<str>db_tb_exist_auto</str>:			Creates a new database and/or database table if they don't exist. You should specify the column names and their data-types within the "CMD" array/object.
<str>new_col</str>:				Creates a new column within a specified database table.
<str>count</str>:					Returns the number of records either in total, or matching the where clause.
<str>db_exist</str>:				Returns a boolean determining if the database exists.
<str>tb_exist</str>:				Returns a boolean determining if the table exists.
<str>drop_db</str>:				Deletes a database.
<str>drop_tb</str>:				Deletes a table.
<str>db-list</str>:				Returns an array of databases on the server.
<str>tb-list</str>:				Returns an array of tables on the server.
<str>backup</str>:					Conducts a backup of the database files to the directory specified within the ini file.
<str>get-types</str>:				Returns an array consisting of the data-types for the columns within a table.
<str>exists</str>:					Returns a boolean indicating if the record exists within the table.
<str>custom</str>:					Executes a custom SQL script.
<str>sql</str>:					Executes a custom SQL script.
</pre>
<br>
<h2>Short-hand Data-Types:</h2>
<br>
<pre>
<str>string</str>
<int>int</int>
<bool>bool</bool>
<var>file</var>
<var>blob</var>
<var>img</var>
</pre>
<br>
<h2>Examples:</h2>
<br>
<b>DB-TB Exists Auto:</b>
<pre>
<var>$db</var>=<str>"start"</str>;
<var>$tb</var>=<str>"accounts"</str>;
<var>$cmd</var>=<method>array</method>(
	<str>"action"</str>=><str>"db_tb_exist_auto"</str>,
	<str>"username"</str>=><str>"string"</str>,
	<str>"password"</str>=><str>"string"</str>,
	<str>"email"</str>=><str>"string"</str>,
	<str>"phone_number"</str>=><str>"string"</str>,
	<str>"timestamp"</str>=><str>"int"</str>
);
<var>$args</var>=<method>array</method>(
	<str>"db"</str>=><var>$db</var>,
	<str>"tb"</str>=><var>$tb</var>,
	<str>"cmd"</str>=><var>$cmd</var>
);
<var>$res</var>=db::<method>connect</method>(<var>$args</var>); <comment>// Should always return true or 1.</comment>
</pre>

<b>Select:</b>
<pre>
<var>$db</var>=<str>"start"</str>;
<var>$tb</var>=<str>"accounts"</str>;
<var>$cmd</var>=<method>array</method>(
	<str>"action"</str>=><str>"select"</str>,
	<str>"username"</str>,
	<str>"password"</str>,
	<str>"email"</str>,
	<str>"phone_number"</str>,
	<str>"timestamp"</str>
);
<comment>// Or...</comment>
<var>$cmd</var>=<method>array</method>(
	<str>"action"</str>=><str>"select"</str>,
	<str>"*"</str>
);
<comment>// (OPTIONAL) Where/Like:</comment>
<var>$where</var>=<method>array</method>(
	<str>"username"</str>=><var>$username</var>,
	<str>"password"</str>=><var>$password</var>
);


<var>$args</var>=<method>array</method>(
	<str>"db"</str>=><var>$db</var>,
	<str>"tb"</str>=><var>$tb</var>,
	<str>"cmd"</str>=><var>$cmd</var>,
	<str>"where"</str>=><var>$where</var>
);
<var>$res</var>=db::<method>connect</method>(<var>$args</var>); <comment>// Returns an array of matching records consisting of the columns specified within the "CMD" array.</comment>
</pre>

<b>Update</b>
<pre>
<div class='db-main'>
	<h1>Setup</h1>
	<h2>ini file</h2>
	<pre>
<var style="color:rgb(200,150,50)">server</var>=<value style="color:rgb(100,150,200)">localhost</value>
<var style="color:rgb(200,150,50)">username</var>=<val style="color:rgb(100,150,200)">root</val>
<var style="color:rgb(200,150,50)">password</var>=
<var style="color:rgb(200,150,50)">port</var>=<bool style="color:rgb(100,100,200)">3389</int>
<var style="color:rgb(200,150,50)">backup_dir</var>=</pre>

<h2>Linking Configuration File To System</h2>
<ol>
	<li>Copy absolute path of ini file.</li>
	<li>Paste path of ini file into class ini method parameter.</li>
</ol>
<pre>
db::<method style="color:rgb(100,200,50)">ini</method>(<var style="color:rgb(200,150,50)">[PATH]</var>);
</pre>
- This can be found within the <b><str style="color:rgb(200,50,255)">db.php</str></b> main file at the bottom.
<br>
<h1>API Documentation</h1>
<h2>Setup</h2>
<br>
The way to implement a database query object is by creating the following...
<pre>
<var style="color:rgb(200,150,50)">$db</var>=<str style="color:rgb(200,50,255)">\"database_name\"</str>;
<var style="color:rgb(200,150,50)">$tb</var>=<str style="color:rgb(200,50,255)">\"database_table_name\"</str>;
<var style="color:rgb(200,150,50)">$cmd</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"action\"</str>=><str style="color:rgb(200,50,255)">\"[ACTION_NAME]\"</str>,
	<str style="color:rgb(200,50,255)">\"column\"</str>,
	<str style="color:rgb(200,50,255)">\"column\"</str>,
	...
);
<comment style="color:rgb(100,100,100)">// The \"WHERE\" and \"LIKE\" clauses/cases are both similar in formatting.</comment>
<var style="color:rgb(200,150,50)">$where</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"[COLUMN_NAME]\"</str>=><str style="color:rgb(200,50,255)">\"[COLUMN_VALUE]\"</str>,
	<str style="color:rgb(200,50,255)">\"[COLUMN_NAME]\"</str>=><str style="color:rgb(200,50,255)">\"[COLUMN_VALUE]\"</str>,
	...
);
<comment style="color:rgb(100,100,100)">// Specifying the \"[OR]\" operator will find records matching the \"ID\" value OR the \"NAME\" value. Mutiple can be included.</comment>
<var style="color:rgb(200,150,50)">$like</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"[or]id\"</str>=><str style="color:rgb(200,50,255)">\"0\"</str>,
	<str style="color:rgb(200,50,255)">\"[or]name\"</str>=><str style="color:rgb(200,50,255)">\"apples\"</str>,
	...
);

<var style="color:rgb(200,150,50)">$args</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"db\"</str>=<var style="color:rgb(200,150,50)">$db</var>,
	<str style="color:rgb(200,50,255)">\"tb\"</str>=<var style="color:rgb(200,150,50)">$tb</var>,
	<str style="color:rgb(200,50,255)">\"cmd\"</str>=<var style="color:rgb(200,150,50)">$cmd</var>,
	<str style="color:rgb(200,50,255)">\"where\"</str>=<var style="color:rgb(200,150,50)">$where</var>
);
<var style="color:rgb(200,150,50)">$res</var>=db::<method style="color:rgb(100,200,50)">connect</method>(<var style="color:rgb(200,150,50)">$args</var>);
</pre>
<br>
- Depending on the specified action, you won't need to specify other properties such as the database name (\"DB\") or even table name...
<br><br>

<h2>Action Names:</h2>
<br>
<pre>
<str style="color:rgb(200,50,255)">select</str>:					Returns an array of values from the specified columns (Not to be confused with the \"WHERE\" or \"LIKE\" clause(s)).
<str style="color:rgb(200,50,255)">insert</str>:					Adds a new record consisting of data specified with the columns and their respective values (Not to be confused with the \"WHERE\" or \"LIKE\" clause(s)).
<str style="color:rgb(200,50,255)">update</str>:					Changes an existing record based on the respective column names and their values, matching a where clause.
<str style="color:rgb(200,50,255)">get_db</str>:					Returns an array consisting of all the databases on the current server.
<str style="color:rgb(200,50,255)">get_tb</str>:					Returns an array consisting of all database tables. Can be used with a where clause.
<str style="color:rgb(200,50,255)">get_type</str>:				Returns the data-type of the specified column names.
<str style="color:rgb(200,50,255)">delete</str>:					Removes either a record, database table, or database depending if there is a where clause specifying which records to delete or not.
<str style="color:rgb(200,50,255)">reset_id</str>:				Resets the unique ID counter that is already generated upon database creation.
<str style="color:rgb(200,50,255)">new_db</str>:					Creates a new database based on the specified name provided within the arguments array/object.
<str style="color:rgb(200,50,255)">new_tb</str>:					Creates a new database table based on the specified name provided within the arguments array/object.
<str style="color:rgb(200,50,255)">db_tb_exist_auto</str>:			Creates a new database and/or database table if they don't exist. You should specify the column names and their data-types within the \"CMD\" array/object.
<str style="color:rgb(200,50,255)">new_col</str>:				Creates a new column within a specified database table.
<str style="color:rgb(200,50,255)">count</str>:					Returns the number of records either in total, or matching the where clause.
<str style="color:rgb(200,50,255)">db_exist</str>:				Returns a boolean determining if the database exists.
<str style="color:rgb(200,50,255)">tb_exist</str>:				Returns a boolean determining if the table exists.
<str style="color:rgb(200,50,255)">drop_db</str>:				Deletes a database.
<str style="color:rgb(200,50,255)">drop_tb</str>:				Deletes a table.
<str style="color:rgb(200,50,255)">db-list</str>:				Returns an array of databases on the server.
<str style="color:rgb(200,50,255)">tb-list</str>:				Returns an array of tables on the server.
<str style="color:rgb(200,50,255)">backup</str>:					Conducts a backup of the database files to the directory specified within the ini file.
<str style="color:rgb(200,50,255)">get-types</str>:				Returns an array consisting of the data-types for the columns within a table.
<str style="color:rgb(200,50,255)">exists</str>:					Returns a boolean indicating if the record exists within the table.
<str style="color:rgb(200,50,255)">custom</str>:					Executes a custom SQL script.
<str style="color:rgb(200,50,255)">sql</str>:					Executes a custom SQL script.
</pre>
<br>
<h2>Short-hand Data-Types:</h2>
<br>
<pre>
<str style="color:rgb(200,50,255)">string</str>
<bool style="color:rgb(100,100,200)">int</int>
<bool style="color:rgb(50,200,150)">bool</bool>
<var style="color:rgb(200,150,50)">file</var>
<var style="color:rgb(200,150,50)">blob</var>
<var style="color:rgb(200,150,50)">img</var>
</pre>
<br>
<h2>Examples:</h2>
<br>
<b>DB-TB Exists Auto:</b>
<pre>
<var style="color:rgb(200,150,50)">$db</var>=<str style="color:rgb(200,50,255)">\"start\"</str>;
<var style="color:rgb(200,150,50)">$tb</var>=<str style="color:rgb(200,50,255)">\"accounts\"</str>;
<var style="color:rgb(200,150,50)">$cmd</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"action\"</str>=><str style="color:rgb(200,50,255)">\"db_tb_exist_auto\"</str>,
	<str style="color:rgb(200,50,255)">\"username\"</str>=><str style="color:rgb(200,50,255)">\"string\"</str>,
	<str style="color:rgb(200,50,255)">\"password\"</str>=><str style="color:rgb(200,50,255)">\"string\"</str>,
	<str style="color:rgb(200,50,255)">\"email\"</str>=><str style="color:rgb(200,50,255)">\"string\"</str>,
	<str style="color:rgb(200,50,255)">\"phone_number\"</str>=><str style="color:rgb(200,50,255)">\"string\"</str>,
	<str style="color:rgb(200,50,255)">\"timestamp\"</str>=><str style="color:rgb(200,50,255)">\"int\"</str>
);
<var style="color:rgb(200,150,50)">$args</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"db\"</str>=><var style="color:rgb(200,150,50)">$db</var>,
	<str style="color:rgb(200,50,255)">\"tb\"</str>=><var style="color:rgb(200,150,50)">$tb</var>,
	<str style="color:rgb(200,50,255)">\"cmd\"</str>=><var style="color:rgb(200,150,50)">$cmd</var>
);
<var style="color:rgb(200,150,50)">$res</var>=db::<method style="color:rgb(100,200,50)">connect</method>(<var style="color:rgb(200,150,50)">$args</var>); <comment style="color:rgb(100,100,100)">// Should always return true or 1.</comment>
</pre>

<b>Select:</b>
<pre>
<var style="color:rgb(200,150,50)">$db</var>=<str style="color:rgb(200,50,255)">\"start\"</str>;
<var style="color:rgb(200,150,50)">$tb</var>=<str style="color:rgb(200,50,255)">\"accounts\"</str>;
<var style="color:rgb(200,150,50)">$cmd</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"action\"</str>=><str style="color:rgb(200,50,255)">\"select\"</str>,
	<str style="color:rgb(200,50,255)">\"username\"</str>,
	<str style="color:rgb(200,50,255)">\"password\"</str>,
	<str style="color:rgb(200,50,255)">\"email\"</str>,
	<str style="color:rgb(200,50,255)">\"phone_number\"</str>,
	<str style="color:rgb(200,50,255)">\"timestamp\"</str>
);
<comment style="color:rgb(100,100,100)">// Or...</comment>
<var style="color:rgb(200,150,50)">$cmd</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"action\"</str>=><str style="color:rgb(200,50,255)">\"select\"</str>,
	<str style="color:rgb(200,50,255)">\"*\"</str>
);
<comment style="color:rgb(100,100,100)">// (OPTIONAL) Where/Like:</comment>
<var style="color:rgb(200,150,50)">$where</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"username\"</str>=><var style="color:rgb(200,150,50)">$username</var>,
	<str style="color:rgb(200,50,255)">\"password\"</str>=><var style="color:rgb(200,150,50)">$password</var>
);


<var style="color:rgb(200,150,50)">$args</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"db\"</str>=><var style="color:rgb(200,150,50)">$db</var>,
	<str style="color:rgb(200,50,255)">\"tb\"</str>=><var style="color:rgb(200,150,50)">$tb</var>,
	<str style="color:rgb(200,50,255)">\"cmd\"</str>=><var style="color:rgb(200,150,50)">$cmd</var>,
	<str style="color:rgb(200,50,255)">\"where\"</str>=><var style="color:rgb(200,150,50)">$where</var>
);
<var style="color:rgb(200,150,50)">$res</var>=db::<method style="color:rgb(100,200,50)">connect</method>(<var style="color:rgb(200,150,50)">$args</var>); <comment style="color:rgb(100,100,100)">// Returns an array of matching records consisting of the columns specified within the \"CMD\" array.</comment>
</pre>

<b>Update</b>
<pre>
<var style="color:rgb(200,150,50)">$db</var>=<str style="color:rgb(200,50,255)">\"start\"</str>;
<var style="color:rgb(200,150,50)">$tb</var>=<str style="color:rgb(200,50,255)">\"accounts\"</str>;
<var style="color:rgb(200,150,50)">$cmd</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"action\"</str>=><str style="color:rgb(200,50,255)">\"update\"</str>,
	<str style="color:rgb(200,50,255)">\"username\"</str>=><str style="color:rgb(200,50,255)">\"apples\"</str>,
	<str style="color:rgb(200,50,255)">\"password\"</str>=><str style="color:rgb(200,50,255)">\"oranges\"</str>,
	<str style="color:rgb(200,50,255)">\"email\"</str>=><str style="color:rgb(200,50,255)">\"pears\"</str>,
	<str style="color:rgb(200,50,255)">\"phone_number\"</str>=><str style="color:rgb(200,50,255)">\"grapes\"</str>,
	<str style="color:rgb(200,50,255)">\"timestamp\"</str>=><bool style="color:rgb(100,100,200)">184784543</int>
);
<var style="color:rgb(200,150,50)">$where</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"username\"</str>=><str style="color:rgb(200,50,255)">\"oranges\"</str>,
	<str style="color:rgb(200,50,255)">\"password\"</str>=><var style="color:rgb(200,150,50)">$password</var>
);
<var style="color:rgb(200,150,50)">$args</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"db\"</str>=><var style="color:rgb(200,150,50)">$db</var>,
	<str style="color:rgb(200,50,255)">\"tb\"</str>=><var style="color:rgb(200,150,50)">$tb</var>,
	<str style="color:rgb(200,50,255)">\"cmd\"</str>=><var style="color:rgb(200,150,50)">$cmd</var>,
	<str style="color:rgb(200,50,255)">\"where\"</str>=><var style="color:rgb(200,150,50)">$where</var>
);
<var style="color:rgb(200,150,50)">$res</var>=db::<method style="color:rgb(100,200,50)">connect</method>(<var style="color:rgb(200,150,50)">$args</var>);
</pre>

<b>Insert:</b>
<pre>
<var style="color:rgb(200,150,50)">$db</var>=<str style="color:rgb(200,50,255)">\"start\"</str>;
<var style="color:rgb(200,150,50)">$tb</var>=<str style="color:rgb(200,50,255)">\"accounts\"</str>;
<var style="color:rgb(200,150,50)">$cmd</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"action\"</str>=><str style="color:rgb(200,50,255)">\"insert\"</str>,
	<str style="color:rgb(200,50,255)">\"username\"</str>=><str style="color:rgb(200,50,255)">\"apples\"</str>,
	<str style="color:rgb(200,50,255)">\"password\"</str>=><str style="color:rgb(200,50,255)">\"oranges\"</str>,
	<str style="color:rgb(200,50,255)">\"email\"</str>=><str style="color:rgb(200,50,255)">\"pears\"</str>,
	<str style="color:rgb(200,50,255)">\"phone_number\"</str>=><str style="color:rgb(200,50,255)">\"grapes\"</str>,
	<str style="color:rgb(200,50,255)">\"timestamp\"</str>=><bool style="color:rgb(100,100,200)">184784543</int>
);
<var style="color:rgb(200,150,50)">$args</var>=<method style="color:rgb(100,200,50)">array</method>(
	<str style="color:rgb(200,50,255)">\"db\"</str>=><var style="color:rgb(200,150,50)">$db</var>,
	<str style="color:rgb(200,50,255)">\"tb\"</str>=><var style="color:rgb(200,150,50)">$tb</var>,
	<str style="color:rgb(200,50,255)">\"cmd\"</str>=><var style="color:rgb(200,150,50)">$cmd</var>
);
<var style="color:rgb(200,150,50)">$res</var>=db::<method style="color:rgb(100,200,50)">connect</method>(<var style="color:rgb(200,150,50)">$args</var>);
</pre>
</div>
