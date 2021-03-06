<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Simple Sidebar - Start Bootstrap Template</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/simple-sidebar.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

<body>

<?php
// Php connections added by David Hughen 2/11/15
// After Andrea Setiawan made modification to the student's html file
session_start();

// Include the constants used for the db connection
require("constants.php");

// 'CSWEB.studentnet.int', 'team1_cs414', 'CS414t1', 'cs414_team_1')

$id = $_SESSION['username']; // Just a random variable gotten from the URL

if($id == null)
    header('Location: login.html');
    
// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);

if (mysqli_connect_errno())
{
   echo "<h1>Connection error</h1>";
}

//query for listing the classes the teacher teaches
/* select class_id from teacher
join class
using(teacher_id)
where teacher_id = ? */


// query for students who have not taken a test:
/* select count(*) from student
join test_list
using(student_id)
join test
using(test_id)
where date_taken is null */

// Class id and description query
$query = "select class_id, class_description from teacher join class using(teacher_id) where teacher_id = ?";

// Student first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from teacher where teacher_id = ?";

// main table query
<<<<<<< HEAD
/* select test.class_id, (select count(student_id) from test_list
						join test using(test_id)
						where date_taken is not null and teacher_id = 121111
						) as num_of_students, update_date 
from test_list
join test
using(test_id)
join teacher
using(teacher_id)
join class
using(teacher_id)
where teacher_id = 121111
group by(test.class_id) */


$tableQuery = "select class_id, c_update, update_date from student
join enrollment using (student_id)
join class using (class_id)
where student_id = ?";

$warningQuery = "select class_id, datediff(date_end, sysdate()) as days_left from enrollment
join class using (class_id)
join test using(class_id)
where student_id = ? and datediff(date_end, sysdate()) < 7 and datediff(date_end, sysdate()) > 0";

// The @ is for ignoring PHP errors. Replace "database_down()" with whatever you want to happen when an error happens.
@ $database->select_db(DATABASENAME);

// The statement variable holds your query      
$stmt = $database->prepare($query);
$topRightStatement = $database->prepare($topRightQuery);
$table = $database->prepare($tableQuery);
$warningstmt = $database->prepare($warningQuery);

?>
	<div id="wrapper2"
	 <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
				<a href="#menu-toggle" class="navbar-brand" id="menu-toggle">
					<div id="logo-area">
						<img src="images/logo4.png" alt="Our Logo" height="45" width="45">
						<span class="TestRepublic">Test Republic</span>
					</div>
				</a>
			</div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i> <b class="caret"></b></a>
                    <ul class="dropdown-menu alert-dropdown">
                        <li>
                            <a href="#">Alert Name <span class="label label-default">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-primary">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-success">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-info">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-warning">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-danger">Alert Badge</span></a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">View All</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php  // Added by David Hughen
																												// to display student's name in top right corner	
																											    $topRightStatement->bind_param("s", $id);
																												$topRightStatement->bind_result($first_name, $last_name);
																												$topRightStatement->execute();
																												while($topRightStatement->fetch())
																												{
																													echo $first_name . " " . $last_name;
																												}
																												$topRightStatement->close(); ?><b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#"><i class="fa fa-fw fa-user"></i> Profile</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fw fa-envelope"></i> Inbox</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fw fa-gear"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->

            <!-- /.navbar-collapse -->
        </nav>
	</div>	
	
    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
				<li>
                    <a href="#" id="student-summary">Summary</a>
                </li>
                <li class="sidebar-brand">
                    Select a Class:
                </li>
               
				<?php 
				// Added by David Hughen
				// The code to fetch the student's classes and put them in the sidebar to the left
				$stmt->bind_param("s", $id);
				$stmt->bind_result($clid, $clde);
				$stmt->execute();
				while($stmt->fetch())
				{
					// WE WILL NEED TO ADD A LINK HERE TO CLASS DETAILS PAGE FOR A TEACHER!!!

					echo '<li><a href="teacherClassPage.php">' . $clid . '<div class="subject-name">' . $clde . '</div></a></li>';

				}
				$stmt->close();
				?>
            </ul>
        </div>
		
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
		<!-- Keep page stuff under this div! -->
            <div class="container-fluid">
                <div class="row">
					<!-- our code starts here :) -->
					<table class="teacher_summary">
					
						<colgroup>
							<col class="classes" />
							<col class="recent_updates" />
							<col class="date" />
						</colgroup>
						
						<thead>
						<tr>
							<th>Classes</th>
							<th>Recent Updates</th>
							<th>Date</th>
						</tr>
						</thead>
						
						<tbody>
						<?php 
							// THE QUERY FOR THE TABLE IN THE MIDDLE OF THE PAGE GOES IN HERE!!!!
							$table->bind_param("s", $id);
							$table->bind_result($clid, $update, $date);
							$table->execute();
							while($table->fetch())
							{	
								echo '<tr><td><button type="button" class="course_button">'.$clid.'</button></td>
									  <td>'.$update.' student(s) took the test.</td>
									  <td>'.$date.'</td></tr>';
							}
							$table->close(); 
							?>			
					</table>
                </div>

            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>

</body>

</html>