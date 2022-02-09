# db
Allows a means to execute SQL queries using PHP arrays/objects.
<div class='db-main'>
	<h1>Setup</h1>
	<h2>ini file</h2>
	<pre>
<var color="rgb(200,150,50)">server</var>=<var color="rgb(100,150,200)">localhost</var>
<var color="rgb(200,150,50)">username</var>=<var color="rgb(100,150,200)">root</var>
<var color="rgb(200,150,50)">password</var>=
<var color="rgb(200,150,50)">port</var>=<var color="rgb(100,100,200)">3389</var>
<var color="rgb(200,150,50)">backup_dir</var>=</pre>

<h2>Linking Configuration File To System</h2>
<ol>
	<li>Copy absolute path of ini file.</li>
	<li>Paste path of ini file into class ini method parameter.</li>
</ol>
<pre>
db::<var color="rgb(100,200,50)">ini</var>(<var color="rgb(200,150,50)">[PATH]</var>);
</pre>
- This can be found within the <b><var color="rgb(200,50,255)">db.php</var></b> main file at the bottom.
<br>
<h1>API Documentation</h1>
<h2>Setup</h2>
<br>
The way to implement a database query object is by creating the following...
<pre>
<var color="rgb(200,150,50)">$db</var>=<var color="rgb(200,50,255)">"database_name"</var>;
<var color="rgb(200,150,50)">$tb</var>=<var color="rgb(200,50,255)">"database_table_name"</var>;
<var color="rgb(200,150,50)">$cmd</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"action"</var>=><var color="rgb(200,50,255)">"[ACTION_NAME]"</var>,
	<var color="rgb(200,50,255)">"column"</var>,
	<var color="rgb(200,50,255)">"column"</var>,
	...
);
<var color="rgb(100,100,100)">// The "WHERE" and "LIKE" clauses/cases are both similar in formatting.</var>
<var color="rgb(200,150,50)">$where</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"[COLUMN_NAME]"</var>=><var color="rgb(200,50,255)">"[COLUMN_VALUE]"</var>,
	<var color="rgb(200,50,255)">"[COLUMN_NAME]"</var>=><var color="rgb(200,50,255)">"[COLUMN_VALUE]"</var>,
	...
);
<var color="rgb(100,100,100)">// Specifying the "[OR]" operator will find records matching the "ID" value OR the "NAME" value. Mutiple can be included.</var>
<var color="rgb(200,150,50)">$like</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"[or]id"</var>=><var color="rgb(200,50,255)">"0"</var>,
	<var color="rgb(200,50,255)">"[or]name"</var>=><var color="rgb(200,50,255)">"apples"</var>,
	...
);

<var color="rgb(200,150,50)">$args</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"db"</var>=<var color="rgb(200,150,50)">$db</var>,
	<var color="rgb(200,50,255)">"tb"</var>=<var color="rgb(200,150,50)">$tb</var>,
	<var color="rgb(200,50,255)">"cmd"</var>=<var color="rgb(200,150,50)">$cmd</var>,
	<var color="rgb(200,50,255)">"where"</var>=<var color="rgb(200,150,50)">$where</var>
);
<var color="rgb(200,150,50)">$res</var>=db::<var color="rgb(100,200,50)">connect</var>(<var color="rgb(200,150,50)">$args</var>);
</pre>
<br>
- Depending on the specified action, you won't need to specify other properties such as the database name ("DB") or even table name...
<br><br>

<h2>Action Names:</h2>
<br>
<pre>
<var color="rgb(200,50,255)">select</var>:					Returns an array of values from the specified columns (Not to be confused with the "WHERE" or "LIKE" clause(s)).
<var color="rgb(200,50,255)">insert</var>:					Adds a new record consisting of data specified with the columns and their respective values (Not to be confused with the "WHERE" or "LIKE" clause(s)).
<var color="rgb(200,50,255)">update</var>:					Changes an existing record based on the respective column names and their values, matching a where clause.
<var color="rgb(200,50,255)">get_db</var>:					Returns an array consisting of all the databases on the current server.
<var color="rgb(200,50,255)">get_tb</var>:					Returns an array consisting of all database tables. Can be used with a where clause.
<var color="rgb(200,50,255)">get_type</var>:				Returns the data-type of the specified column names.
<var color="rgb(200,50,255)">delete</var>:					Removes either a record, database table, or database depending if there is a where clause specifying which records to delete or not.
<var color="rgb(200,50,255)">reset_id</var>:				Resets the unique ID counter that is already generated upon database creation.
<var color="rgb(200,50,255)">new_db</var>:					Creates a new database based on the specified name provided within the arguments array/object.
<var color="rgb(200,50,255)">new_tb</var>:					Creates a new database table based on the specified name provided within the arguments array/object.
<var color="rgb(200,50,255)">db_tb_exist_auto</var>:			Creates a new database and/or database table if they don't exist. You should specify the column names and their data-types within the "CMD" array/object.
<var color="rgb(200,50,255)">new_col</var>:				Creates a new column within a specified database table.
<var color="rgb(200,50,255)">count</var>:					Returns the number of records either in total, or matching the where clause.
<var color="rgb(200,50,255)">db_exist</var>:				Returns a boolean determining if the database exists.
<var color="rgb(200,50,255)">tb_exist</var>:				Returns a boolean determining if the table exists.
<var color="rgb(200,50,255)">drop_db</var>:				Deletes a database.
<var color="rgb(200,50,255)">drop_tb</var>:				Deletes a table.
<var color="rgb(200,50,255)">db-list</var>:				Returns an array of databases on the server.
<var color="rgb(200,50,255)">tb-list</var>:				Returns an array of tables on the server.
<var color="rgb(200,50,255)">backup</var>:					Conducts a backup of the database files to the directory specified within the ini file.
<var color="rgb(200,50,255)">get-types</var>:				Returns an array consisting of the data-types for the columns within a table.
<var color="rgb(200,50,255)">exists</var>:					Returns a boolean indicating if the record exists within the table.
<var color="rgb(200,50,255)">custom</var>:					Executes a custom SQL script.
<var color="rgb(200,50,255)">sql</var>:					Executes a custom SQL script.
</pre>
<br>
<h2>Short-hand Data-Types:</h2>
<br>
<pre>
<var color="rgb(200,50,255)">string</var>
<var color="rgb(100,100,200)">int</var>
<var color="rgb(50,200,150)">bool</var>
<var color="rgb(200,150,50)">file</var>
<var color="rgb(200,150,50)">blob</var>
<var color="rgb(200,150,50)">img</var>
</pre>
<br>
<h2>Examples:</h2>
<br>
<b>DB-TB Exists Auto:</b>
<pre>
<var color="rgb(200,150,50)">$db</var>=<var color="rgb(200,50,255)">"start"</var>;
<var color="rgb(200,150,50)">$tb</var>=<var color="rgb(200,50,255)">"accounts"</var>;
<var color="rgb(200,150,50)">$cmd</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"action"</var>=><var color="rgb(200,50,255)">"db_tb_exist_auto"</var>,
	<var color="rgb(200,50,255)">"username"</var>=><var color="rgb(200,50,255)">"string"</var>,
	<var color="rgb(200,50,255)">"password"</var>=><var color="rgb(200,50,255)">"string"</var>,
	<var color="rgb(200,50,255)">"email"</var>=><var color="rgb(200,50,255)">"string"</var>,
	<var color="rgb(200,50,255)">"phone_number"</var>=><var color="rgb(200,50,255)">"string"</var>,
	<var color="rgb(200,50,255)">"timestamp"</var>=><var color="rgb(200,50,255)">"int"</var>
);
<var color="rgb(200,150,50)">$args</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"db"</var>=><var color="rgb(200,150,50)">$db</var>,
	<var color="rgb(200,50,255)">"tb"</var>=><var color="rgb(200,150,50)">$tb</var>,
	<var color="rgb(200,50,255)">"cmd"</var>=><var color="rgb(200,150,50)">$cmd</var>
);
<var color="rgb(200,150,50)">$res</var>=db::<var color="rgb(100,200,50)">connect</var>(<var color="rgb(200,150,50)">$args</var>); <var color="rgb(100,100,100)">// Should always return true or 1.</var>
</pre>

<b>Select:</b>
<pre>
<var color="rgb(200,150,50)">$db</var>=<var color="rgb(200,50,255)">"start"</var>;
<var color="rgb(200,150,50)">$tb</var>=<var color="rgb(200,50,255)">"accounts"</var>;
<var color="rgb(200,150,50)">$cmd</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"action"</var>=><var color="rgb(200,50,255)">"select"</var>,
	<var color="rgb(200,50,255)">"username"</var>,
	<var color="rgb(200,50,255)">"password"</var>,
	<var color="rgb(200,50,255)">"email"</var>,
	<var color="rgb(200,50,255)">"phone_number"</var>,
	<var color="rgb(200,50,255)">"timestamp"</var>
);
<var color="rgb(100,100,100)">// Or...</var>
<var color="rgb(200,150,50)">$cmd</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"action"</var>=><var color="rgb(200,50,255)">"select"</var>,
	<var color="rgb(200,50,255)">"*"</var>
);
<var color="rgb(100,100,100)">// (OPTIONAL) Where/Like:</var>
<var color="rgb(200,150,50)">$where</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"username"</var>=><var color="rgb(200,150,50)">$username</var>,
	<var color="rgb(200,50,255)">"password"</var>=><var color="rgb(200,150,50)">$password</var>
);


<var color="rgb(200,150,50)">$args</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"db"</var>=><var color="rgb(200,150,50)">$db</var>,
	<var color="rgb(200,50,255)">"tb"</var>=><var color="rgb(200,150,50)">$tb</var>,
	<var color="rgb(200,50,255)">"cmd"</var>=><var color="rgb(200,150,50)">$cmd</var>,
	<var color="rgb(200,50,255)">"where"</var>=><var color="rgb(200,150,50)">$where</var>
);
<var color="rgb(200,150,50)">$res</var>=db::<var color="rgb(100,200,50)">connect</var>(<var color="rgb(200,150,50)">$args</var>); <var color="rgb(100,100,100)">// Returns an array of matching records consisting of the columns specified within the "CMD" array.</var>
</pre>

<b>Update</b>
<pre>
<var color="rgb(200,150,50)">$db</var>=<var color="rgb(200,50,255)">"start"</var>;
<var color="rgb(200,150,50)">$tb</var>=<var color="rgb(200,50,255)">"accounts"</var>;
<var color="rgb(200,150,50)">$cmd</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"action"</var>=><var color="rgb(200,50,255)">"update"</var>,
	<var color="rgb(200,50,255)">"username"</var>=><var color="rgb(200,50,255)">"apples"</var>,
	<var color="rgb(200,50,255)">"password"</var>=><var color="rgb(200,50,255)">"oranges"</var>,
	<var color="rgb(200,50,255)">"email"</var>=><var color="rgb(200,50,255)">"pears"</var>,
	<var color="rgb(200,50,255)">"phone_number"</var>=><var color="rgb(200,50,255)">"grapes"</var>,
	<var color="rgb(200,50,255)">"timestamp"</var>=><var color="rgb(100,100,200)">184784543</var>
);
<var color="rgb(200,150,50)">$where</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"username"</var>=><var color="rgb(200,50,255)">"oranges"</var>,
	<var color="rgb(200,50,255)">"password"</var>=><var color="rgb(200,150,50)">$password</var>
);
<var color="rgb(200,150,50)">$args</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"db"</var>=><var color="rgb(200,150,50)">$db</var>,
	<var color="rgb(200,50,255)">"tb"</var>=><var color="rgb(200,150,50)">$tb</var>,
	<var color="rgb(200,50,255)">"cmd"</var>=><var color="rgb(200,150,50)">$cmd</var>,
	<var color="rgb(200,50,255)">"where"</var>=><var color="rgb(200,150,50)">$where</var>
);
<var color="rgb(200,150,50)">$res</var>=db::<var color="rgb(100,200,50)">connect</var>(<var color="rgb(200,150,50)">$args</var>);
</pre>

<b>Insert:</b>
<pre>
<var color="rgb(200,150,50)">$db</var>=<var color="rgb(200,50,255)">"start"</var>;
<var color="rgb(200,150,50)">$tb</var>=<var color="rgb(200,50,255)">"accounts"</var>;
<var color="rgb(200,150,50)">$cmd</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"action"</var>=><var color="rgb(200,50,255)">"insert"</var>,
	<var color="rgb(200,50,255)">"username"</var>=><var color="rgb(200,50,255)">"apples"</var>,
	<var color="rgb(200,50,255)">"password"</var>=><var color="rgb(200,50,255)">"oranges"</var>,
	<var color="rgb(200,50,255)">"email"</var>=><var color="rgb(200,50,255)">"pears"</var>,
	<var color="rgb(200,50,255)">"phone_number"</var>=><var color="rgb(200,50,255)">"grapes"</var>,
	<var color="rgb(200,50,255)">"timestamp"</var>=><var color="rgb(100,100,200)">184784543</var>
);
<var color="rgb(200,150,50)">$args</var>=<var color="rgb(100,200,50)">array</var>(
	<var color="rgb(200,50,255)">"db"</var>=><var color="rgb(200,150,50)">$db</var>,
	<var color="rgb(200,50,255)">"tb"</var>=><var color="rgb(200,150,50)">$tb</var>,
	<var color="rgb(200,50,255)">"cmd"</var>=><var color="rgb(200,150,50)">$cmd</var>
);
<var color="rgb(200,150,50)">$res</var>=db::<var color="rgb(100,200,50)">connect</var>(<var color="rgb(200,150,50)">$args</var>);
</pre>
</div>
