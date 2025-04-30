<?php include('db_connect.php') ?>
<?php
$twhere ="";
if($_SESSION['login_type'] != 1)
  $twhere = "  ";
?>
<!-- Info boxes -->
 <div class="col-12">
          <div class="card animate-card">
            <div class="card-body">
              <h4 class="mb-2 welcome-text">Welcome to AixPM</h4>
              <p class="text-muted welcome-subtext">Hello, <?php echo $_SESSION['login_name'] ?>! Let's manage your projects efficiently.</p>
            </div>
          </div>
  </div>
  <hr>
  <?php 

    $where = "";
    if($_SESSION['login_type'] == 2){
      $where = " where manager_id = '{$_SESSION['login_id']}' ";
    }elseif($_SESSION['login_type'] == 3){
      $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
    }
     $where2 = "";
    if($_SESSION['login_type'] == 2){
      $where2 = " where p.manager_id = '{$_SESSION['login_id']}' ";
    }elseif($_SESSION['login_type'] == 3){
      $where2 = " where concat('[',REPLACE(p.user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
    }
    ?>
        
      <div class="row">
        <div class="col-md-8">
        <div class="card card-outline card-success animate-card">
          <div class="card-header">
            <b>Project Progress</b>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table m-0 table-hover">
                <colgroup>
                  <col width="5%">
                  <col width="30%">
                  <col width="35%">
                  <col width="15%">
                  <col width="15%">
                </colgroup>
                <thead>
                  <th>#</th>
                  <th>Project</th>
                  <th>Progress</th>
                  <th>Status</th>
                  <th></th>
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
                  $prog= 0;
                $tprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']}")->num_rows;
                $cprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']} and status = 3")->num_rows;
                $prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
                $prog = $prog > 0 ?  number_format($prog,2) : $prog;
                $prod = $conn->query("SELECT * FROM user_productivity where project_id = {$row['id']}")->num_rows;
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
                      <td>
                        <a class="btn btn-primary btn-sm" href="./index.php?page=view_project&id=<?php echo $row['id'] ?>">
                              <i class="fas fa-folder">
                              </i>
                              View
                        </a>
                      </td>
                  </tr>
                <?php endwhile; ?>
                </tbody>  
              </table>
            </div>
          </div>
        </div>
        </div>
        <div class="col-md-4">
          <div class="row">
          <div class="col-12 col-sm-6 col-md-12">
            <div class="small-box bg-light shadow-sm border stats-box">
              <div class="inner">
                <h3 class="counter"><?php echo $conn->query("SELECT * FROM project_list $where")->num_rows; ?></h3>
                <p>Total Projects</p>
              </div>
              <div class="icon">
                <i class="fa fa-layer-group"></i>
              </div>
            </div>
          </div>
           <div class="col-12 col-sm-6 col-md-12">
            <div class="small-box bg-light shadow-sm border stats-box">
              <div class="inner">
                <h3 class="counter"><?php echo $conn->query("SELECT t.*,p.name as pname,p.start_date,p.status as pstatus, p.end_date,p.id as pid FROM task_list t inner join project_list p on p.id = t.project_id $where2")->num_rows; ?></h3>
                <p>Total Tasks</p>
              </div>
              <div class="icon">
                <i class="fa fa-tasks"></i>
              </div>
            </div>
          </div>
      </div>
        </div>
      </div>

<style>
  /* Optimize card animations */
  .animate-card {
    animation: slideInUp 0.5s ease-out;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    will-change: transform;
  }
  
  /* Optimize table animations */
  .table tbody tr {
    transition: transform 0.3s ease, background-color 0.3s ease;
    will-change: transform;
  }

  /* Optimize badge animations */
  .badge {
    transition: transform 0.3s ease;
    will-change: transform;
  }

  /* Optimize small box animations */
  .small-box {
    transition: transform 0.3s ease;
    will-change: transform;
    animation: slideInRight 0.5s ease-out;
  }

  /* Reduce number of properties being animated */
  .small-box:hover {
    transform: translateY(-5px);
  }

  /* Optimize icon animations */
  .small-box .icon {
    transition: transform 0.3s ease;
    will-change: transform;
  }

  /* Welcome card animation */
  .welcome-text {
    animation: fadeIn 0.8s ease-out;
  }

  .welcome-subtext {
    animation: fadeIn 1s ease-out 0.3s backwards;
  }

  /* Progress bar animation */
  .progress-bar {
    transition: width 1s ease-in-out;
  }

  /* Table row hover effect */
  .table tbody tr:hover {
    transform: translateX(5px);
    background-color: rgba(0,0,0,0.02);
  }

  /* Status badge animation */
  .badge:hover {
    transform: scale(1.1);
  }

  /* Animations */
  @keyframes slideInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(20px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }

  /* Stats box improvements */
  .stats-box {
    position: relative;
    transition: all 0.3s ease;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px;
    background: linear-gradient(145deg, #ffffff, #f6f6f6);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  }

  .stats-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  }

  .stats-box .inner {
    padding: 20px;
    position: relative;
    z-index: 2;
  }

  .stats-box .inner h3 {
    font-size: 2.2rem;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 10px;
    position: relative;
    z-index: 2;
  }

  .stats-box .inner p {
    color: #7f8c8d;
    font-size: 1.1rem;
    margin-bottom: 0;
  }

  .stats-box .icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;
  }

  .stats-box .icon i {
    font-size: 65px;
    opacity: 0.2;
    transition: all 0.3s ease;
    color: #2c3e50;
    line-height: 0;
  }

  .stats-box:hover .icon i {
    opacity: 0.4;
    transform: scale(1.1);
  }

  /* Adjust inner content to prevent overlap */
  .stats-box .inner {
    padding-right: 80px;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .stats-box .icon i {
      font-size: 50px;
    }
    
    .stats-box .inner {
      padding-right: 60px;
    }
  }
</style>

<script>
    // Add animation when stats boxes come into view
    $(document).ready(function() {
        $('.stats-box').each(function(i) {
            var $stats = $(this);
            setTimeout(function() {
                $stats.addClass('animated');
            }, i * 200); // Stagger the animation
        });
    });
</script>
