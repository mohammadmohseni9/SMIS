<?php
    include("../Include/dbconnect.php");
    include("../Include/checklogin.php");
    $errormsg= '';
    $branch='';
    if(isset($_POST['save']))
    {
        $introduction = mysqli_real_escape_string($conn, $_POST['introduction']);
        $os = mysqli_real_escape_string($conn, $_POST['os']);
        $msword = mysqli_real_escape_string($conn, $_POST['msword']);
        $msexcel = mysqli_real_escape_string($conn, $_POST['msexcel']);
        $msaccess = mysqli_real_escape_string($conn, $_POST['msaccess']);
        $mspowerpoint = mysqli_real_escape_string($conn, $_POST['mspowerpoint']);
        $mspublishing = mysqli_real_escape_string($conn, $_POST['mspublishing']);
        $internet = mysqli_real_escape_string($conn, $_POST['internet']);
        $submitdate = mysqli_real_escape_string($conn,$_POST['submitdate']);
        $transaction_remark = mysqli_real_escape_string($conn,$_POST['transaction_remark']);
        $sid = mysqli_real_escape_string($conn,$_POST['sid']);

        $sql = "select fees,balance  from student where id = '$sid'";
        
        $sql = "UPDATE  exam SET introduction = '$introduction', os='$os', msword='$msword', msexcel='$msexcel', msaccess='$msaccess', mspowerpoint='$mspowerpoint', mspublishing='$mspublishing', internet='$internet', transaction_remark='$transaction_remark' WHERE stdid = '$sid' ";
        $conn->query($sql);
        echo '<script type="text/javascript">window.location="exam.php?act=1";</script>';
    }

    if(isset($_REQUEST['act']) && @$_REQUEST['act']=="1")
    {
        $errormsg = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Success!</strong> Examination score Submitted successfully</div>";
    }

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Exam Marks Entry | SMIS</title>
    <?php include("../Include/links.php"); ?>

    <style type="text/css">
        h4{
            text-align:center;
        }
    </style>
</head>
<?php
include("../Include/header.php");
?>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Examination Details</h1>
                    </div>
                </div>
                <?php
                echo $errormsg;
                ?>

                <div class="row" style="margin-bottom:20px;">
                    <div class="col-md-12">
                        <fieldset class="scheduler-border" >
                            <legend  class="scheduler-border">Search:</legend>
                            <form class="form-inline" role="form" id="searchform">
                                <div class="form-group">
                                    <label for="email">Name</label>
                                    <input type="text" class="form-control" id="student" name="student">
                                </div>
  
                                <div class="form-group">
                                    <label for="email"> D.O.J </label>
                                    <input type="text" class="form-control" id="doj" name="doj" >
                                </div>
  
                                <div class="form-group">
                                    <label for="email"> Branch </label>
                                    <select  class="form-control" id="branch" name="branch" >
                                        <option value="" >Select Branch</option>
                                        <?php
                                            $sql = "select * from branch where delete_status='0' order by branch.branch asc";
                                            $q = $conn->query($sql);
                                            
                                            while($r = $q->fetch_assoc())
                                            {
                                            echo '<option value="'.$r['id'].'"  '.(($branch==$r['id'])?'selected="selected"':'').'>'.$r['branch'].'</option>';
                                            }
                                        ?>
	                                </select>
                                </div>
  
                                <button type="button" class="btn btn-success btn-sm" id="find" > Find </button>
                                <button type="reset" class="btn btn-danger btn-sm" id="clear" > Clear </button>
                            </form>
                        </fieldset>

                    </div>
                </div>

                <script type="text/javascript">
                    $(document).ready( function() {	
                        $("#doj").datepicker({
                            
                            changeMonth: true,
                            changeYear: true,
                            showButtonPanel: true,
                            dateFormat: 'mm/yy',
                            onClose: function(dateText, inst) {
                                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
                            }
                        });

                        $("#doj").focus(function () {
                            $(".ui-datepicker-calendar").hide();
                            $("#ui-datepicker-div").position({
                                my: "center top",
                                at: "center bottom",
                                of: $(this)
                                });
                        });

                        /*****************/
	
                        $('#student').autocomplete({
                            source: function( request, response ) {
                                $.ajax({
                                    url : '../data-tables/ajax.php',
                                    dataType: "json",
                                    data: {
                                        name_startsWith: request.term,
                                        type: 'studentname'
                                        },
                                    success: function( data ) {                              
                                        response( $.map( data, function( item ) {                                  
                                            return {
                                                label: item,
                                                value: item
                                            }
                                        }));
                                    }	
                                });
                            }
                            /*,
                            autoFocus: true,
                            minLength: 0,
                            select: function( event, ui ) {
                                    var abc = ui.item.label.split("-");
                                    //alert(abc[0]);
                                    $("#student").val(abc[0]);
                                    return false;

                                    },
                             */
		                });
                        $('#find').click(function () {
                            mydatatable();
                        });
                        $('#clear').click(function () {
                            $('#searchform')[0].reset();
                            mydatatable();
                        });
                                    
                        function mydatatable()
                            {     
                                $("#subjectresult").html('<table class="table table-striped table-bordered table-hover" id="tSortable22"><thead><tr><th>Name/Contact</th><th>Fees</th><th>Balance</th><th>Branch</th><th>DOJ</th><th>Action</th></tr></thead><tbody></tbody></table>');
                                
                                    $("#tSortable22").dataTable({
                                        'sPaginationType' : 'full_numbers',
                                        "bLengthChange": false,
                                        "bFilter": false,
                                        "bInfo": false,
                                        'bProcessing' : true,
                                        'bServerSide': true,
                                        'sAjaxSource': "../../data-tables/datatable.php?"+$('#searchform').serialize()+"&type=examsearch",
                                        'aoColumnDefs': [{
                                            'bSortable': false,
                                            'aTargets': [-1] /* 1st one, start by the right */
                                            }]
                                    });
                            }
		
                            ////////////////////////////
                        $("#tSortable22").dataTable({
                                            
                            'sPaginationType' : 'full_numbers',
                            "bLengthChange": false,
                            "bFilter": false,
                            "bInfo": false,                                          
                            'bProcessing' : true,
                            'bServerSide': true,
                            'sAjaxSource': "../../data-tables/datatable.php?type=examsearch",                                       
                            'aoColumnDefs': [{
                                'bSortable': false,
                                'aTargets': [-1] /* 1st one, start by the right */
                            }]
                        });

                            ///////////////////////////		                                
                    });


                    function GetExamForm(sid)
                        {
                            $.ajax({
                                type: 'post',
                                url: 'getexamform.php',
                                data: {student:sid,req:'1'},
                                success: function (data) {
                                    $('#formcontent').html(data);
                                    $("#myModal").modal({backdrop: "static"});
                                    }
                                });
                        }

                </script>

                <style>
                    #doj .ui-datepicker-calendar
                    {
                    display:none;
                    }
                </style>
		
		        <div class="panel panel-primary">
                    <div class="panel-heading">
                            Manage Examination Details  
                    </div>
                        <div class="panel-body">
                            <div class="table-sorting table-responsive" id="subjectresult">
                                <table class="table table-striped table-bordered table-hover" id="tSortable22">
                                    <thead>
                                        <tr>
                                          
                                            <th>Name/Contact</th>                                            
                                            <th>Fees</th>
											<th>Balance</th>
											<th>Branch</th>
											<th>DOJ</th>
											<th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
								    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
	                <!-------->
	                <!-- Modal -->
                    <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header text-center">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title w-100">Examination Results Entry</h4>
                                </div>
                                <div class="modal-body" id="formcontent">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>	
    	          
            </div>
            <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
    <!-- /. WRAPPER  -->

    <?php
        include("../Include/footer.php");
    ?>
   
  
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="../../js/bootstrap.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="../../js/jquery.metisMenu.js"></script>
    <!-- CUSTOM SCRIPTS -->
    <script src="../../js/custom1.js"></script>    
</body>
</html>
