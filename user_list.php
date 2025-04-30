<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New User</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Name</th>
						<th>Email</th>
						<th>Role</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$type = array('',"Admin","Project Manager","Employee");
					$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users order by concat(firstname,' ',lastname) asc");
					while($row= $qry->fetch_assoc()):
					?>
					<tr class="animate-row">
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo ucwords($row['name']) ?></b></td>
						<td><b><?php echo $row['email'] ?></b></td>
						<td><b><?php echo $type[$row['type']] ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" style="">
		                      <a class="dropdown-item view_user" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_user&id=<?php echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_user" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		// Add row animation delay
		$('.animate-row').each(function(index) {
			$(this).css('--row-index', index + 1);
		});

		// Initialize DataTable
		$('#list').dataTable();

		// Initialize Bootstrap dropdowns
		$('.dropdown-toggle').dropdown({
			boundary: 'window'
		});

		// Add dropup class for dropdowns near bottom of viewport
		$('.dropdown-toggle').on('show.bs.dropdown', function () {
			var $button = $(this);
			var buttonOffset = $button.offset().top;
			var buttonHeight = $button.outerHeight();
			var dropdownHeight = $(this).next('.dropdown-menu').outerHeight();
			var windowHeight = $(window).height();
			
			if ((buttonOffset + buttonHeight + dropdownHeight) > windowHeight) {
				$button.parent().addClass('dropup');
			} else {
				$button.parent().removeClass('dropup');
			}
		});

		// Existing handlers
		$('.view_user').click(function(){
			uni_modal("<i class='fa fa-id-card'></i> User Details","view_user.php?id="+$(this).attr('data-id'));
		});

		$('.delete_user').click(function(){
			_conf("Are you sure to delete this user?","delete_user",[$(this).attr('data-id')]);
		});
	});

	function delete_user($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_user',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}
</script>
<style>
table p{
    margin: unset !important;
}
table td{
    vertical-align: middle !important
}

/* Simple, effective dropdown styling */
.dropdown-menu {
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #ddd;
}

.dropdown-item {
    padding: 8px 20px;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.btn-default.dropdown-toggle {
    transition: all 0.3s ease;
}

.btn-default.dropdown-toggle:hover {
    background: #f8f9fa;
}
</style>