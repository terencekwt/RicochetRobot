<?
$db_addr = 'hittrendhk.db';     // address of MySQL server.
$db_user = 'robot';		// Username to access server.
$db_pass = 'robot';		// Password access server.
$db_name = 'ricochet_robot';	// Name of database to connect to.
$connect = @mysql_connect("$db_addr", "$db_user", "$db_pass");

if (!($connect)) // If no connect, error and exit().
{
     echo("<p>Unable to connect to the database server.</p>");
     exit();
}

if (!(@mysql_select_db($db_name))) // If can't connect to database, error and exit().
{
     echo("<p>Unable to locate the $db_name database.</p>");
     exit();
}
//else echo "connected";

?>