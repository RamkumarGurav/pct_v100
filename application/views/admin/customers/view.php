<?php

$page_module_name = "Customers";

?>
<?
$name="";
$customers_id=0;
$status=1;
$record_action = "Add New Record";
if(!empty($customers_data))
{
	// $record_action = "Update";
	// $customers_id = $customers_data->customers_id;
	// $name = $customers_data->name;
	// $status = $customers_data->status;
	
}
?>
<!-- /.navbar -->

<!-- Main Sidebar Container -->


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?=$page_module_name?> <small>Details</small></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?=MAINSITE_Admin."wam"?>">Home</a></li>
						<li class="breadcrumb-item"><a href="<?=MAINSITE_Admin.$user_access->class_name."/".$user_access->function_name?>"><?=$user_access->module_name?> List</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <?  ?>
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="card ">

                    <div class="card-header">
                        <h3 class="card-title"><?=$customers_data->name?></h3>
                        <div class="float-right">
                            <?php 
								if($user_access->add_module==1 && false)	{
								?>
								<a href="<?=MAINSITE_Admin.$user_access->class_name?>/edit"> 
                            <button type="button" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                                New</button></a>
                            <? } ?>
                            <?php 
							if($user_access->update_module==1)	{
							?>
							<a href="<?=MAINSITE_Admin.$user_access->class_name?>/edit/<?=$customers_data->customers_id?>"> 
                            <button type="button" class="btn btn-success btn-sm" ><i
                                    class="fas fa-edit"></i> Update</button>
                            </a>
                            <? } ?>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <?php 
						if($user_access->view_module==1)	{
					?>
                    <div class="card-body card-primary card-outline">
                        
                            <?php echo form_open(MAINSITE_Admin."$user_access->class_name/doUpdateStatus", array('method' => 'post', 'id' => 'ptype_list_form' , "name"=>"ptype_list_form", 'style' => '', 'class' => 'form-horizontal', 'role' => 'form', 'enctype' => 'multipart/form-data')); ?>
                            <input type="hidden" name="task" id="task" value="" />
                            <? echo $this->session->flashdata('alert_message'); ?>
                            <div class="divTable" style="display:none;">
                            	<div class="TableRow">
                                	<div class="table_col">
                                        <label class="label_content_br">Data Base Id <span class="colen">:</span></label>
                                        <?=$customers_data->customers_id?>
                                    </div>
                                    <div class="table_col table_wrap">
                                        <label class="label_content_br">Customers Date  <span class="colen">:</span></label>
                                       <?=date("d-m-Y h:i:s A" , strtotime($customers_data->added_on))?>
                                    </div>
                                    <div class="table_col" >
                                        <label class="label_content_br">Name <span class="colen">:</span></label>
                                        <?=$customers_data->name?>
                                    </div>                                	
                                   
                                </div>                                 
                            	<div class="TableRow">
                                	<div class="table_col">
                                        <label class="label_content_br">Email<span class="colen">:</span></label>
                                        <? echo $customers_data->email;?>
                                    </div>
                                    
                                    <div class="table_col">
                                        <label class="label_content_br">Contact No.<span class="colen">:</span></label>
                                        <?=$customers_data->number?>
                                    </div>
                                    
                                    <div class="table_col">
                                        <label class="label_content_br">Description<span class="colen">:</span></label>
                                        <?=$customers_data->description?>
                                    </div>
                                    
                                    
                                     
                                </div>                                  
                            	<div class="TableRow">
                                	
                                    <div class="table_col">
                                        <label class="label_content_br">Added On<span class="colen">:</span></label>
                                        <?=date("d-m-Y h:i:s A" , strtotime($customers_data->added_on))?>
                                    </div>
                                    
                                    <div class="table_col">
                                        <label class="label_content_br">Updated On<span class="colen">:</span></label>
                                        <? if(!empty($customers_data->updated_on)){echo date("d-m-Y h:i:s A" , strtotime($customers_data->updated_on));}else{echo "-";}?>
                                    </div>
                                </div>                           
                            	<div class="TableRow">
                                	<div class="table_col">
                                        <label class="label_content_br">Updated By<span class="colen">:</span></label>
                                        <? if(!empty($customers_data->updated_by_name)){echo $customers_data->updated_by_name;}else{echo "-";}?>
                                    </div>
                                	<div class="table_col">
                                        <label class="label_content_br">Status<span class="colen">:</span></label>
                                        <? if($customers_data->status==1){ ?> Active <i class="fas fa-check btn-success btn-sm "></i>
                                                <?}else{ ?> Block <i class="fas fa-ban btn-danger btn-sm "></i>
                                                <? }?>
                                    </div>
                               </div>                            
                            </div>
                            
                            <table id="" class="table table-bordered table-hover myviewtable responsiveTableNewDesign"  >
                                <tbody>
								<tr>
										<td >
										<strong class="full">Data Base Id</strong>
										<?=$customers_data->customers_id?></td>
										
										<td >
                                        	<strong class="full">Contact No. Verified</strong>
                                            <? if($customers_data->is_contact_verify==1){ ?> Yes <i class="fas fa-check btn-success btn-sm "></i> <? }else{ ?> No <i class="fas fa-ban btn-danger btn-sm "></i><? }?>
										</td>
<td >
										<strong class="full">Email Verified</strong>
										<? if($customers_data->is_email_verify==1){ ?> Yes <i class="fas fa-check btn-success btn-sm "></i> <? }else{ ?> No <i class="fas fa-ban btn-danger btn-sm "></i><? }?>
										
									</tr>

									<tr>
                                        <td >
										<strong class="full">Name</strong>
										<?=$customers_data->name?></td>
                                        
                                        <td >
                                        <strong  class="full">Email</strong>
                                        <?=$customers_data->email?></td>
                                        <td >
                                        <strong class="full">Contact No</strong>
                                        <?=$customers_data->number?></td>
                                        
                                       
									</tr>
                                    
									<tr>
										
										
										
									</tr>
									
									
                                    
                                    <tr>
                                    <td >
										<strong class="full">Updated On</strong>
										<? if(!empty($customers_data->updated_on)){echo date("d-m-Y h:i:s A" , strtotime($customers_data->updated_on));}else{echo "-";}?></td>
										<td >
                                        	<strong class="full">Registered On</strong>
                                            <?=date("d-m-Y h:i:s A" , strtotime($customers_data->added_on))?>
										</td>
										<td colspan="3">
										<strong class="full">Status</strong>
										<? if($customers_data->status==1){ ?> Active <i class="fas fa-check btn-success btn-sm "></i>
                                                <?}else{ ?> Block <i class="fas fa-ban btn-danger btn-sm "></i>
                                                <? }?></td>
									</tr>
                                    
                                </tbody>
                                
						</table>
						<?php echo form_close() ?>
                    </div>
                    <? }else{ 
						$this->data['no_access_flash_message']="You Dont Have Access To View ".$page_module_name;
						$this->load->view('admin/template/access_denied' , $this->data); 
					} ?>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>


    </section>
    <?  ?>
</div>

<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>

<script type="application/javascript">
function check_uncheck_All_records() // done
{
    var mainCheckBoxObj = document.getElementById("main_check");
    var checkBoxObj = document.getElementsByName("sel_recds[]");

    for (var i = 0; i < checkBoxObj.length; i++) {
        if (mainCheckBoxObj.checked)
            checkBoxObj[i].checked = true;
        else
            checkBoxObj[i].checked = false;
    }
}

function validateCheckedRecordsArray() // done
{
    var checkBoxObj = document.getElementsByName("sel_recds[]");
    var count = true;

    for (var i = 0; i < checkBoxObj.length; i++) {
        if (checkBoxObj[i].checked) {
            count = false;
            break;
        }
    }

    return count;
}

function validateRecordsActivate() // done
{
    if (validateCheckedRecordsArray()) {
        //alert("Please select any record to activate.");
        toastrDefaultErrorFunc("Please select any record to activate.");
        document.getElementById("sel_recds1").focus();
        return false;
    } else {
        document.ptype_list_form.task.value = 'active';
        document.ptype_list_form.submit();
    }
}

function validateRecordsBlock() // done
{
    if (validateCheckedRecordsArray()) {
        //alert("Please select any record to block.");
        toastrDefaultErrorFunc("Please select any record to block.");
        document.getElementById("sel_recds1").focus();
        return false;
    } else {
        document.ptype_list_form.task.value = 'block';
        document.ptype_list_form.submit();
    }
}
</script>
