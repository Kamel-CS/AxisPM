<?php include 'db_connect.php' ?>
 <div class="col-md-12">
        <div class="card card-outline card-success">
          <div class="card-header">
            <b>Project Progress</b>
            <div class="card-tools">
            	<button class="btn btn-flat btn-sm bg-gradient-success btn-success" id="print"><i class="fa fa-print"></i> Print</button>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive" id="printable">
              <table class="table m-0 table-bordered">
               <!--  <colgroup>
                  <col width="5%">
                  <col width="30%">
                  <col width="35%">
                  <col width="15%">
                  <col width="15%">
                </colgroup> -->
                <thead>
                  <th>#</th>
                  <th>Project</th>
                  <th>Task</th>
                  <th>Completed Task</th>
                  <th>Work Duration</th>
                  <th>Progress</th>
                  <th>Status</th>
                </thead>
                <tbody>
                <?php
                $i = 1;
                $stat = array("Pending","Started","On-Progress","On-Hold","Over Due","Done");
                $where = "";
                if($_SESSION['login_type'] == 2){
                  $where = " where manager_id = '{$_SESSION['login_id']}' ";
                }elseif($_SESSION['login_type'] == 3){
                  $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
                }
                $qry = $conn->query("SELECT * FROM project_list $where order by name asc");
                while($row= $qry->fetch_assoc()):
                $tprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']}")->num_rows;
                $cprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']} and status = 3")->num_rows;
                $prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
                $prog = $prog > 0 ?  number_format($prog,2) : $prog;
                $prod = $conn->query("SELECT * FROM user_productivity where project_id = {$row['id']}")->num_rows;
                $dur = $conn->query("SELECT sum(time_rendered) as duration FROM user_productivity where project_id = {$row['id']}");
                $dur = $dur->num_rows > 0 ? $dur->fetch_assoc()['duration'] : 0;
                if($row['status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])):
                if($prod  > 0  || $cprog > 0)
                  $row['status'] = 2;
                else
                  $row['status'] = 1;
                elseif($row['status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])):
                $row['status'] = 4;
                endif;
                  ?>
                  <tr>
                      <td>
                         <?php echo $i++ ?>
                      </td>
                      <td>
                          <a>
                              <?php echo ucwords($row['name']) ?>
                          </a>
                          <br>
                          <small>
                              Due: <?php echo date("Y-m-d",strtotime($row['end_date'])) ?>
                          </small>
                      </td>
                      <td class="text-center">
                      	<?php echo number_format($tprog) ?>
                      </td>
                      <td class="text-center">
                      	<?php echo number_format($cprog) ?>
                      </td>
                      <td class="text-center">
                      	<?php echo number_format($dur).' Hr/s.' ?>
                      </td>
                      <td class="project_progress">
                          <div class="progress progress-sm">
                              <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $prog ?>%">
                              </div>
                          </div>
                          <small>
                              <?php echo $prog ?>% Complete
                          </small>
                      </td>
                      <td class="project-state">
                          <?php
                            if($stat[$row['status']] =='Pending'){
                              echo "<span class='badge badge-secondary'>{$stat[$row['status']]}</span>";
                            }elseif($stat[$row['status']] =='Started'){
                              echo "<span class='badge badge-primary'>{$stat[$row['status']]}</span>";
                            }elseif($stat[$row['status']] =='On-Progress'){
                              echo "<span class='badge badge-info'>{$stat[$row['status']]}</span>";
                            }elseif($stat[$row['status']] =='On-Hold'){
                              echo "<span class='badge badge-warning'>{$stat[$row['status']]}</span>";
                            }elseif($stat[$row['status']] =='Over Due'){
                              echo "<span class='badge badge-danger'>{$stat[$row['status']]}</span>";
                            }elseif($stat[$row['status']] =='Done'){
                              echo "<span class='badge badge-success'>{$stat[$row['status']]}</span>";
                            }
                          ?>
                      </td>
                  </tr>
                <?php endwhile; ?>
                </tbody>  
              </table>
            </div>
          </div>
        </div>
        </div>
<script>
$('#print').click(function(){
    // Start loading animation (if applicable)
    start_load();

    // Clone the printable content
    var _p = $('#printable').clone();

    // Add a header to the printable content
    var _d = "<p class='text-center'><b>Project Progress Report as of <?php echo date('F d, Y') ?></b></p>";
    _p.prepend(_d);

    // Open a new window for printing
    var nw = window.open("", "_blank", "width=900,height=600");

    // Write the content to the new window
    nw.document.write("<html><head><title>Print</title>");
    nw.document.write("<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css'>"); // Include Bootstrap for table styling
    nw.document.write("<style>body { font-family: Arial, sans-serif; }</style>"); // Optional inline styles
    nw.document.write("</head><body>");
    nw.document.write(_p.html());
    nw.document.write("</body></html>");

    // Close the document to ensure it renders
    nw.document.close();

    // Trigger the print dialog
    nw.print();

    // Close the window after printing
    setTimeout(function(){
        nw.close();
        end_load(); // End loading animation (if applicable)
    }, 750);
});
</script>