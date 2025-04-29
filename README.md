<h1>About</h1>
<p>This project is about to help you with your invoice creation and tracking.</p>
<p>In this application you can create invoices, save it to database, export it to PDF, edit it, keep track of it (by its status) and delete it from database. Also, your customers are also sent to database and their data saved for future use.</p>
<p>The application is in early stage and there are going to be much more features in the future.</p>

<h1>Instalation</h1>
<p>For using this application you need to do few steps:</p>
<ol>
  <li>Install apache.</li>
  <li>Clone the git repository into your appache htdocs folder.</li>
  <li>Go to project folder "Backend/SQL/" and run db_structure.sql into database.</li>
  <li>(optional) Create user for database</li>
  <li>Go to project file "Backend/globals.php" and edit database connection credentials (dbname, username, password) in variable "$preset_db->AppInvoice"</li>
  <li>Go to project file "components/settings.php" and check the credentials for creating path to the folder where is the source code located.</li>
</ol>
<p>After this you should be good to go.</p>

<h1>Warning - things to check</h1>
<p>If the system isn't working you may try to define the server path (path where the source code is located) manualy ( $server_url = 'http://localhost/AppInvoice'; ) in these files:</p>
<ul>
  <li>Functions/BackendConnect.php on line 8</li>
  <li>BackendForm.php on line 11</li>
  <li>p_admin.php on line 7</li>
</ul>
<p>If the system is still not working, feel free to contact me.</p>

<h1>Test data</h1>
<p>For testing this application we provided the test data that can be imported in database. For importing the data go to project folder "Backend/SQL/" and run test_data.sql into database. After this you should have the test data in your database.</p>
<p>To login as administrator use:</p>
<ul>
  <li>Username: admin@appinvoice.sk</li>
  <li>Password: admin</li>
</ul>
<p>To login as common user use:</p>
<ul>
  <li>Username: user@appinvoice.sk</li>
  <li>Password: password</li>
</ul>

<h1>Note</h1>
<p>For more information see documentation.docx</p>

<h2>Enjoy!</h2>
