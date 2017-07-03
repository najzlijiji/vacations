<div class="row content">
	<div class="col-sm-4">
		<h2 id="title"><small>Add New User</small></h2>
	</div>
</div>
<hr>
<div class="adduser" id="adduser">
	<div class="panel panel-default">
		<div class="panel-heading">Enter details and Save</div>
			<div class="panel-body">
				<form name="date" id="date" class="form-Horizontal" onsubmit="add_user();">
					<div class="form-group" id='nameerror'>
						<label class="col-sm-2 control-label">Name</label>
						<div class="col-sm-4">
							<input type="text" id="name" name="name" class="form-control">
						</div>
					</div>
					<div class="form-group" id="nameerror">
						<label class="col-sm-2 control-label">Max Number of Vacation Days</label>
						<div class="col-sm-4">
							<input type="text" id="days" name="days" class="form-control">
						</div>
					</div>
					<br />
					<input type='submit' class="btn btn-success" value='Save' style="float:right">
				</form>
			</div>
		</div>
	</div>
</div>