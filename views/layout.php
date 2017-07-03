<html>
<head>
    <title>Vacations</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>  
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="js/main.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav">
                <h4>Vacations Page</h4>
                <ul class="nav nav-pills nav-stacked" id='menu'>
                <li id='approved'><a href="#approved"onclick="load_page('vacations','approved');">Approved Vacations</a></li>
                <li id='rejected'><a href="#rejected" onclick="load_page('vacations','rejected');">Rejected Vacations</a></li>
                <li id='request'><a href="#request" onclick="load_page('vacations','request');">Request Vacation</a></li>
                <li id='pending'><a href="#pending" onclick="load_page('vacations','pending');">Approve Vacations <span class="badge"></span></a></li>
                <li id='addnew'><a href="#addnew" onclick="load_page('users','addnew');">Add New User</a></li>
                </ul><br>
            </div>
            <div class="col-sm-10">
                <div  id='content'>
                </div>
            </div>
            <div id="error" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-body">
                        <div class='alert alert-danger' id="error-div">
                            <span id="errortxt"></span>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>          
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>