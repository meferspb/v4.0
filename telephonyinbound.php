<?php

	###################################################
	### Name: telephonyinbound.php 					###
	### Functions: Manage Inbound, IVR & DID  		###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
	### License: AGPLv2								###
	###################################################

	require_once('./php/UIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Inbound</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>
    	
    	<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
       
        <?php print $ui->creamyThemeCSS(); ?>
		
		<!-- Bootstrap Color Picker -->
  		<link rel="stylesheet" href="adminlte/colorpicker/bootstrap-colorpicker.min.css">
		<!-- Wizard Form style -->
    	<link rel="stylesheet" href="css/easyWizard.css">
		
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        
        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>

    </head>
    
    <?php print $ui->creamyBody(); ?>

        <div class="wrapper">	
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("inbound_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("inbound"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>

<?php
	/*
	 * APIs used
	 */

	$ingroup = $ui->API_getInGroups();

	$ivr = $ui->API_getIVR();
	
	$phonenumber = $ui->API_getPhoneNumber();

?>
			<div class="panel panel-default">
				<div class="panel-body">
					<legend>Inbound: <small>Ingroups, Call Menus, Phone Numbers</small> </legend>

		            <div role="tabpanel">
						
						<ul role="tablist" class="nav nav-tabs nav-justified">

						 <!-- In-group panel tabs-->
							 <li role="presentation" class="active">
								<a href="#T_ingroup" aria-controls="T_ingroup" role="tab" data-toggle="tab" class="bb0">
								    In-Groups</a>
							 </li>
						<!-- IVR panel tab -->
							 <li role="presentation">
								<a href="#T_ivr" aria-controls="T_ivr" role="tab" data-toggle="tab" class="bb0">
								    Interactive Voice Response (IVR) Menus </a>
							 </li>
						<!-- DID panel tab -->
							 <li role="presentation">
								<a href="#T_phonenumber" aria-controls="T_phonenumber" role="tab" data-toggle="tab" class="bb0">
								    Phone Numbers (DIDs/TFNs) </a>
							 </li>
						  </ul>
						  
						<!-- Tab panes-->
						<div class="tab-content bg-white">

							<!--==== In-group ====-->
							<div id="T_ingroup" role="tabpanel" class="tab-pane active">
								<table class="table table-striped table-bordered table-hover" id="table_ingroup">
								   <thead>
									  <tr>
										 <th>In-Group</th>
										 <th class='hide-on-low hide-on-medium'>Descriptions</th>
										 <th class='hide-on-low hide-on-medium'>Priority</th>
										 <th class='hide-on-low'>Status</th>
										 <th class='hide-on-low hide-on-medium'>Time</th>
										 <th>Action</th>
									  </tr>
								   </thead>
								   <tbody>
									   	<?php
									   		for($i=0;$i < count($ingroup->group_id);$i++){
							
												if($ingroup->active[$i] == "Y"){
													$ingroup->active[$i] = "Active";
												}else{
													$ingroup->active[$i] = "Inactive";
												}

											$action_INGROUP = $ui->getUserActionMenuForInGroups($ingroup->group_id[$i]);

									   	?>	
											<tr>
												<td><a class='edit-ingroup' data-id="<?php echo $ingroup->group_id[$i];?>"><?php echo $ingroup->group_id[$i];?></a></td>
												<td class='hide-on-low hide-on-medium'><?php echo $ingroup->group_name[$i];?></td>
												<td class='hide-on-low hide-on-medium'><?php echo $ingroup->queue_priority[$i];?></td>
												<td class='hide-on-low'><?php echo $ingroup->active[$i];?></td>
												<td class='hide-on-low hide-on-medium'><?php echo $ingroup->call_time_id[$i];?></td>
												<td><?php echo $action_INGROUP;?></td>
											</tr>
										<?php
											}
										?>
								   </tbody>
								</table>
							</div>
							
							<!--==== IVR ====-->
							<div id="T_ivr" role="tabpanel" class="tab-pane">
								<table class="table table-striped table-bordered table-hover" id="table_ivr">
								   <thead>
									  <tr>
										 <th>Menu ID</th>
										 <th class='hide-on-medium hide-on-low'>Descriptions</th>
										 <th class='hide-on-medium hide-on-low'>Prompt</th>
										 <th class='hide-on-medium hide-on-low'>Timeout</th>
										 <th>Action</th>
									  </tr>
								   </thead>
								   <tbody>
									   	<?php
									   		for($i=0;$i < count($ivr->menu_id);$i++){

											$action_IVR = $ui->ActionMenuForIVR($ivr->menu_id[$i], $ivr->menu_name[$i]);

									   	?>	
											<tr>
												<td><a class='edit-ivr' data-id="<?php echo $ivr->menu_id[$i];?>"><?php echo $ivr->menu_id[$i];?></a></td>
												<td class='hide-on-medium hide-on-low'><?php echo $ivr->menu_name[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $ivr->menu_prompt[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $ivr->menu_timeout[$i];?></td>
												<td><?php echo $action_IVR;?></td>
											</tr>
										<?php
											}
										?>
								   </tbody>
								</table>
							</div>

							<!--==== phonenumber / DID ====-->
							<div id="T_phonenumber" class="tab-pane">
								<table class="table table-striped table-bordered table-hover" id="table_did">
								   <thead>
									  <tr>
										 <th>Phone Numbers</th>
										 <th class='hide-on-medium hide-on-low'>Description</th>
										 <th class='hide-on-medium hide-on-low'>Status</th>
										 <th class='hide-on-medium hide-on-low'>Route</th>
										 <th>Action</th>
									  </tr>
								   </thead>
								   <tbody>
									   	<?php
									   		for($i=0;$i < count($phonenumber->did_pattern);$i++){

									   			if($phonenumber->active[$i] == "Y"){
													$phonenumber->active[$i] = "Active";
												}else{
													$phonenumber->active[$i] = "Inactive";
												}

												if($phonenumber->did_route[$i] == "IN_GROUP"){
													$phonenumber->did_route[$i] = "IN-GROUP";
												}
												if($phonenumber->did_route[$i] == "EXTEN"){
													$phonenumber->did_route[$i] = "CUSTOM EXTENSION";
												}

											$action_DID = $ui->getUserActionMenuForDID($phonenumber->did_id[$i], $phonenumber->did_description[$i]);

									   	?>	
											<tr>
												<td><a class='edit-phonenumber' data-id="<?php echo $phonenumber->did_id[$i];?>"><?php echo $phonenumber->did_pattern[$i];?></a></td>
												<td class='hide-on-medium hide-on-low'><?php echo $phonenumber->did_description[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $phonenumber->active[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $phonenumber->did_route[$i];?></td>
												<td><?php echo $action_DID;?></td>
											</tr>
										<?php
											}
										?>
								   </tbody>
								</table>
							</div>

						</div><!-- END tab content-->

							<!-- /fila con acciones, formularios y demás -->
							<?php
								} else {
									print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
								}
							?>
							
						<div class="bottom-menu skin-blue">
							<div class="action-button-circle" data-toggle="modal">
								<?php print $ui->getCircleButton("inbound", "plus"); ?>
							</div>
							<div class="fab-div-area" id="fab-div-area">
								<ul class="fab-ul" style="height: 250px;">
									<li class="li-style"><a class="fa fa-users fab-div-item" data-toggle="modal" data-target="#add_ingroups"></a></li><br/>
									<li class="li-style"><a class="fa fa-volume-up fab-div-item" data-toggle="modal" data-target="#add_ivr"></a></li><br/>
									<li class="li-style"><a class="fa fa-phone-square fab-div-item" data-toggle="modal" data-target="#add_phonenumbers"> </a></li>
								</ul>
							</div>
						</div>
					</div>
				</div><!-- /. body -->
			</div><!-- /. panel -->
        </section><!-- /.content -->
    </aside><!-- /.right-side -->
</div><!-- ./wrapper -->

<?php
	/*
	 * APIs for getting lists for the some of the forms
	 */
	$users = $ui->API_goGetAllUserLists();
	$user_groups = $ui->API_goGetUserGroupsList();
	$ingroups = $ui->API_getInGroups();
	$voicemails = $ui->API_goGetVoiceMails();
	$phones = $ui->API_getPhonesList();
	$ivr = $ui->API_getIVR();
	$scripts = $ui->API_goGetAllScripts();
	$voicefiles = $ui->API_GetVoiceFilesList();
?>


<!-- TELEPHONY INBOUND MODALS -->

	<!-- ADD INGROUP MODAL -->
		<div class="modal fade" id="add_ingroups" tabindex="-1" aria-labelledby="ingroup_modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">

			<!-- Header -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="ingroup_modal"><b>In-Group Wizard » Create New Ingroup</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="AddTelephonyIngroup.php" method="POST" id="create_ingroup" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="form-group">
							<label class="col-sm-3 control-label" for="groupid">Group ID:</label>
							<div class="col-sm-9">
								<input type="text" name="groupid" id="groupid" class="form-control" placeholder="Group ID. This is a required field. No Spaces. 2-20 characters in length." maxlength="20" minlength="2">
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-3 control-label" for="groupname">Group Name: </label>
							<div class="col-sm-9">
								<input type="text" name="groupname" id="groupname" class="form-control" placeholder="Group Name. This is a required field. 2-20 characters in length." maxlength="20" minlength="2">
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-3 control-label" for="color">Group Color: </label>
							<div class="col-sm-9">
					            <input type="text" class="form-control colorpicker" name="color" id="color" value="#fffff">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="user_group">User Group: </label>
							<div class="col-sm-9">
								<select id="user_group" class="form-control" name="user_group">
									<?php
										for($i=0;$i<count($user_groups->user_group);$i++){
									?>
										<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i]." - ".$user_groups->group_name[$i];?>  </option>
									<?php
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="active">Active: </label>
							<div class="col-sm-9">
								<select name="active" id="active" class="form-control">
									<option value="Y" selected>Yes</option>
									<option value="N">No</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="web_form">Web Form: </label>
							<div class="col-sm-9">
								<input type="url" name="web_form" id="web_form" class="form-control" placeholder="Place a valid URL here... ">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="ingroup_voicemail">Voicemail: </label>
							<div class="col-sm-9">	
								<select name="ingroup_voicemail" id="ingroup_voicemail" class="form-control">
									<?php
										if($voicemails == NULL){
									?>
										<option value="" selected>--No Voicemails Available--</option>
									<?php
										}else{
										for($i=0;$i<count($voicemails->voicemail_id);$i++){
									?>
											<option value="<?php echo $voicemails->voicemail_id[$i];?>">
												<?php echo $voicemails->voicemail_id[$i].' - '.$voicemails->fullname[$i];?>
											</option>									
									<?php
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="next_agent_call">Next Agent Call: </label>
							<div class="col-sm-9">	
								<select name="next_agent_call" id="next_agent_call" class="form-control">
										<option value="random"> Random </option>
										<option value="oldest_call_start"> Oldest Call Start </option>
										<option value="oldest_call_finish"> Oldest Call Finish </option>
										<option value="overall_user_level"> Overall User Lever </option>
										<option value="inbound_group_rank"> Inbound Group Rank </option>
										<option value="campaign_rank"> Campaign Rank </option>
										<option value="fewest_calls"> Fewest Calls </option>
										<option value="fewest_calls_campaign"> Fewest Calls Campaign </option>
										<option value="longest_wait_time"> Longest Wait Time </option>
										<option value="ring_all"> Ring All </option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="display">Fronter Display: </label>
							<div class="col-sm-9">
								<select name="display" id="display" class="form-control">
									<option value="N" selected>No</option>
									<option value="Y">Yes</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="script">Script: </label>
							<div class="col-sm-9">	
								<select name="script" id="script" class="form-control">
									<option value="NONE">--- NONE --- </option>
									<?php
										for($i=0;$i<count($scripts->script_id);$i++){
									?>
										<option value="<?php echo $scripts->script_id[$i];?>">
											<?php echo $scripts->script_id[$i].' - '.$scripts->script_name[$i];?>
										</option>									
									<?php
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="call_launch">Get Call Launch: </label>
							<div class="col-sm-9">	
								<select name="call_launch" id="call_launch" class="form-control">
										<option value="NONE"> NONE </option>
										<option value="SCRIPT"> SCRIPT </option>
										<option value="WEBFORM"> WEBFORM </option>
										<option value="WEBFORMTWO"> WEBFORMTWO </option>
										<option value="FORM"> FORM </option>
										<option value="EMAIL"> EMAIL </option>
								</select>
							</div>
						</div>
					</div><!-- end of step -->
				
				</form>

				</div> <!-- end of modal body -->

				<!-- NOTIFICATIONS -->
				<div id="notifications">
					<div class="output-message-success" style="display:none;">
						<div class="alert alert-success alert-dismissible" role="alert">
						  <strong>Success!</strong> New Ingroup added !
						</div>
					</div>
					<div class="output-message-error" style="display:none;">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <span id="ingroup_result"></span>
						</div>
					</div>
					<div class="output-message-incomplete" style="display:none;">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  Please fill-up all the fields correctly and do not leave any fields with (<strong> * </strong>) blank.
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<!-- The wizard button will be inserted here. -->
					<button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
					<input type="submit" class="btn btn-primary" id="submit_ingroup" value="Submit" style="display: inline-block;">
				</div>
			</div>
		</div>
	</div><!-- end of modal -->
	
	<!-- ADD IVR MODAL -->
		<div class="modal fade" id="add_ivr" tabindex="-1" aria-labelledby="ivr_modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">
					
				<div class="modal-header">					
					<button type="button" class="close" data-dismiss="modal" aria-label="close_did"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="ivr_modal"><b>Call Menu Wizard » Create New Call Menu</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="AddTelephonyIVR.php" method="POST" id="create_ivr" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step" style="display: block;">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="menu_id">Menu ID:</label>
							<div class="col-sm-6">
								<input type="text" name="menu_id" id="menu_id" class="form-control" placeholder="Menu ID" minlength="4" required title="No Spaces. Minimum of 4 characters">
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_name">Menu Name: </label>
							<div class="col-sm-6">
								<input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="Menu Name" required>
							</div>
						</div>
						
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_prompt">Menu Greeting: </label>
							<div class="col-sm-7">
								<select name="menu_prompt" id="menu_prompt" class="form-control">
									<option value="goWelcomeIVR" selected>-- Default Value --</option>
									<?php
										for($i=0;$i<count($voicefiles->file_name);$i++){
											$file = substr($voicefiles->file_name[$i], 0, -4);
									?>
										<option value="<?php echo $file;?>"><?php echo $file;?></option>		
									<?php
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_timeout">Menu Timeout: </label>
							<div class="col-sm-4">
								<input type="number" name="menu_timeout" id="menu_timeout" class="form-control" value="10" min="0" required>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_timeout_prompt">Menu Timeout Greeting: </label>
							<div class="col-sm-6">
								<select name="menu_timeout_prompt " id="menu_timeout_prompt" class="form-control">
									<option value="" selected>-- Default Value --</option>
									<?php
										for($i=0;$i<count($voicefiles->file_name);$i++){
											$file = substr($voicefiles->file_name[$i], 0, -4);
									?>
										<option value="<?php echo $file;?>"><?php echo $file;?></option>		
									<?php
										}
									?>				
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_invalid_prompt">Menu Invalid Greeting: </label>
							<div class="col-sm-7">
								<select name="menu_invalid_prompt" id="menu_invalid_prompt" class="form-control">
									<option value="" selected>-- Default Value --</option>	
									<?php
										for($i=0;$i<count($voicefiles->file_name);$i++){
											$file = substr($voicefiles->file_name[$i], 0, -4);
									?>
										<option value="<?php echo $file;?>"><?php echo $file;?></option>		
									<?php
										}
									?>				
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_repeat">Menu Repeat: </label>
							<div class="col-sm-7">
								<input type="number" name="menu_repeat" id="menu_repeat" class="form-control" value="1" min="0" required>
							</div>
						</div>
						
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_time_check">Menu Time Check: </label>
							<div class="col-sm-5">
								<select name="menu_time_check" id="menu_time_check" class="form-control">
									<option value="ADMIN" > Select Menu Time Check </option>		
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="call_time">Call Time: </label>
							<div class="col-sm-5">
								<select name="call_time" id="call_time" class="form-control">
									<option value="ADMIN" > Select Call Time </option>		
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_repeat">Track call in realtime report: </label>
							<div class="col-sm-5"> 
								<select name="call_time" id="call_time" class="form-control">
									<option value="ADMIN" > Select Track Call </option>		
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="tracking_group">Tracking Group: </label>
							<div class="col-sm-7">
								<select name="tracking_group" id="tracking_group" class="form-control">
								<?php
									for($i=0;$i<count($ingroups->group_id);$i++){
								?>
									<option value="<?php echo $ingroups->group_id[$i];?>">
										<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
									</option>									
								<?php
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="user_groups">User Groups: </label>
							<div class="col-sm-5">
								<select name="user_groups" id="user_groups" class="form-control">
									<?php
										for($i=0;$i<count($user_groups->user_group);$i++){
									?>
										<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->group_name[$i];?>  </option>
									<?php
										}
									?>		
								</select>
							</div>
						</div>
					</div><!-- End of Step -->

				<!-- STEP 2 -->
					<div class="wizard-step" style="display: block;">
						<div class="form-group">
							<div class="col-lg-12">
								<div class="pull-right">
									<button type="button" class="btn btn-primary add-option">Add Option</button>
								</div>
							</div>
						</div>
						<div class="form-group to-clone-opt">
							<label class="col-sm-3 control-label" for="">Default Call Menu Entry:</label>
							<div class="col-lg-2">
								Option:
								<select class="form-control">
									<option selected>TIMEOUT</option>
								</select>
							</div>
							<div class="col-lg-2">
								Desription: 
								<input type="text" name="" id="" class="form-control" placeholder="Description" required value="Hangup">
							</div>
							<div class="col-lg-2">
								Route:
								<select class="form-control">
									<option selected>Hangup</option>
								</select>
							</div>
							<div class="col-lg-2">
								Audio File:
								<input type="text" name="" id="" class="form-control" placeholder="Description" required value="vm-goodbye">
							</div>
							<div class="col-lg-1 btn-remove"></div>
						</div>
						<div class="cloning-area"></div>
					</div><!-- End of Step -->
				

				</div> <!-- end of modal body -->
				</form>

				<!-- NOTIFICATIONS -->
					<div class="output-message-success hide">
						<div class="alert alert-success alert-dismissible" role="alert">
						  <strong>Success!</strong> New Call Menu added.
						</div>
					</div>
					<div class="output-message-error hide">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <strong><span id="ivr_result"></span></strong> Something went wrong, please see input data on form or if agent already exists.
						</div>
					</div>
					<div class="output-message-incomplete hide">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <strong>Incomplete!</strong> Something went wrong, please complete all the fields below.
						</div>
					</div>

				<div class="modal-footer wizard-buttons">
					<!-- The wizard button will be inserted here. -->
				</div>
				
				
			</div>
		</div>
	</div><!-- end of modal -->

	
	<!-- ADD DID MODAL -->
		<div class="modal fade" id="add_phonenumbers" tabindex="-1" aria-labelledby="did_modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="close_did"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="did_modal"><b>DID Wizard » Create new DID</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="AddTelephonyPhonenumber.php" method="POST" id="create_phonenumber" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step" style="display: block;">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="did_exten">DID Extention:</label>
							<div class="col-sm-7">
								<input type="text" name="did_exten" id="did_exten" class="form-control" placeholder="DID Extention" maxlength="20" minlength="2" required title="No Spaces. 2-20 characters in length.">
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="desc">DID Description: </label>
							<div class="col-sm-7">
								<input type="text" name="desc" id="desc" class="form-control" placeholder="DID Description" maxlength="20" minlength="2" required title="2-20 characters in length">
							</div>
						</div>
						
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="active">Active: </label>
							<div class="col-sm-5">
								<select name="active" id="active" class="form-control">
									<option value="Y" selected>Yes</option>
									<option value="N">No</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="route" >DID Route: </label>
							<div class="col-sm-6">
								<select class="form-control" id="route" name="route">
									<option value="AGENT"> Agent </option>
									<option value="IN_GROUP"> In-group </option>
									<option value="PHONE"> Phone </option>
									<option value="CALLMENU"> Call Menu / IVR </option>
									<option value="VOICEMAIL"> Voicemail </option>
									<option value="EXTEN"> Custom Extension </option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="user_groups">User Groups: </label>
							<div class="col-sm-7">
								<select name="user_groups" id="user_groups" class="form-control">
									<?php
										for($i=0;$i<count($user_groups->user_group);$i++){
									?>
										<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i]." - ".$user_groups->group_name[$i];?>  </option>
									<?php
										}
									?>
								</select>
							</div>
						</div>
						
				<!-- IF DID ROUTE = AGENT-->

					<div id="form_route_agent">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="route_agentid">Agent ID: </label>
							<div class="col-sm-7">	
								<select name="route_agentid" id="route_agentid" class="form-control">
									<option value="" > -- NONE -- </option>
									<?php
										for($i=0;$i<count($users->userno);$i++){
									?>
										<option value="<?php echo $users->userno[$i];?>">
											<?php echo $users->userno[$i].' - '.$users->full_name[$i];?>
										</option>									
									<?php
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="route_unavail">Agent Unavailable Action: </label>
							<div class="col-sm-7">	
								<select name="route_unavail" id="route_unavail" class="form-control">
									<option value="VOICEMAIL" > Voicemail </option>
									<option value="PHONE" > Phone </option>
									<option value="IN_GROUP" > In-group </option>
									<option value="EXTEN" > Custom Extension </option>
								</select>
							</div>
						</div>
					</div><!-- end of div agent-->
					
				<!-- IF DID ROUTE = IN-GROUP-->
				
					<div id="form_route_ingroup" style="display: none;">
						<label class="col-sm-4 control-label" for="route_ingroupid">In-Group ID: </label>
						<div class="col-sm-8">	
							<select name="route_ingroupid" id="route_ingroupid" class="form-control">
								<?php
									for($i=0;$i<count($ingroups->group_id);$i++){
								?>
									<option value="<?php echo $ingroups->group_id[$i];?>">
										<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
									</option>									
								<?php
									}
								?>
							</select>
						</div>
					</div><!-- end of ingroup div -->
					
				<!-- IF DID ROUTE = PHONE -->

					<div id="form_route_phone" style="display: none;">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="route_phone_exten">Phone Extension: </label>
							<div class="col-sm-7">	
								<select name="route_phone_exten" id="route_phone_exten" class="form-control">
									<?php
										for($i=0;$i<count($phones->extension);$i++){
									?>
										<option value="<?php echo $phones->extension[$i];?>">
											<?php echo $phones->extension[$i].' - '.$phones->server_ip[$i].' - '.$phones->dialplan_number[$i];?>
										</option>									
									<?php
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="route_phone_server">Server IP: </label>
							<div class="col-sm-7">	
								<select name="route_phone_server" id="route_phone_server" class="form-control">
									<option value="" > -- NONE -- </option>
									<?php
										for($i=0;$i < 1;$i++){
									?>
										<option value="<?php echo $phones->server_ip[$i];?>">
											<?php echo 'GOautodial - '.$phones->server_ip[$i];?>
										</option>									
									<?php
										}
									?>
								</select>
							</div>
						</div>
					</div><!-- end of phone div -->
					
				<!-- IF DID ROUTE = IVR -->

					<div id="form_route_callmenu" style="display: none;">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="route_ivr">Call Menu: </label>
							<div class="col-sm-7">	
								<select name="route_ivr" id="route_ivr" class="form-control">
									<?php
									if(count($ivr->menu_id) > 0){
										for($i=0;$i<count($ivr->menu_id);$i++){
									?>
										<option value="<?php echo $ivr->menu_id[$i];?>">
											<?php echo $ivr->menu_id[$i].' - '.$ivr->menu_name[$i];?>
										</option>									
									<?php
										}
									}else{
									?>
										<option value="">- - - No Available Call Menu - - - </option>
									<?php
									}
									?>
								</select>
							</div>
						</div>
					</div><!-- end of ivr div -->
					
				<!-- IF DID ROUTE = VoiceMail -->

					<div id="form_route_voicemail" style="display: none;">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="route_voicemail">Voicemail Box: </label>
							<div class="col-sm-7">	
								<select name="route_voicemail" id="route_voicemail" class="form-control">
									
									<?php
										for($i=0;$i<count($voicemails->voicemail_id);$i++){
									?>
										<option value="<?php echo $voicemails->voicemail_id[$i];?>">
											<?php echo $voicemails->voicemail_id[$i].' - '.$voicemails->fullname[$i];?>
										</option>									
									<?php
										}
									?>
									
								</select>
							</div>
						</div>
					</div><!-- end of voicemail div -->
					
					<!-- IF DID ROUTE = Custom Extension -->

					<div id="form_route_exten" style="display: none;">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="route_exten">Extension: </label>
							<div class="col-sm-7">
								<input type="text" name="route_exten" id="route_exten" placeholder="Extension" class="form-control" required>
							</div>
							<label class="col-sm-4 control-label" for="route_exten_context">Extension Context: </label>
							<div class="col-sm-7">
								<input type="text" name="route_exten_context" id="route_exten_context" placeholder="Extension Context" class="form-control" required>
							</div>
						</div>
					</div><!-- end of custom extension div -->
					
					</div><!-- End of Step -->
				
				

				</div> <!-- end of modal body -->
				</form>

				<!-- NOTIFICATIONS -->

				<div class="output-message-success" style="display:none;">
					<div class="alert alert-success alert-dismissible" role="alert">
					  <strong>Success!</strong> New Phone Number added.
					</div>
				</div>
				<div class="output-message-error" style="display:none;">
					<div class="alert alert-danger alert-dismissible" role="alert">
						<span id="phonenumber_result"></span></strong> 
					</div>
				</div>
				<div class="output-message-incomplete" style="display:none;">
					<div class="alert alert-danger alert-dismissible" role="alert">
					  <strong>Incomplete!</strong> Something went wrong, please complete all the fields below.
					</div>
				</div>
				
				<div class="modal-footer">
					<!-- The wizard button will be inserted here. -->
					<button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
					<input type="submit" class="btn btn-primary" id="submit_did" value="Submit" style="display: inline-block;">
					
				</div>
			</div>
		</div>
	</div><!-- end of modal -->

<!-- END OF TELEPHONY INBOUND MODALS -->

	<!-- DELETE VALIDATION MODAL -->
	<div id="delete_validation_modal" class="modal modal-warning fade">
    	<div class="modal-dialog">
            <div class="modal-content" style="border-radius:5px;margin-top: 40%;">
				<div class="modal-header">
					<h4 class="modal-title"><b>WARNING!</b>  You are about to <b><u>DELETE</u></b> a <span class="action_validation"></span>... </h4>
				</div>
				<div class="modal-body" style="background:#fff;">
					<p>This action cannot be undone.</p>
					<p>Are you sure you want to delete <span class="action_validation"></span>: <i><b style="font-size:20px;"><span class="delete_extension"></span></b></i> ?</p>
				</div>
				<div class="modal-footer" style="background:#fff;">
					<button type="button" class="btn btn-primary id-delete-label" id="delete_yes">Yes</button>
               		<button type="button" class="btn btn-default pull-left" data-dismiss="modal">No</button>
              </div>
			</div>
		</div>
	</div>

	<!-- DELETE NOTIFICATION MODAL -->
	<div id="delete_notification" style="display:none;">
		<?php echo $ui->deleteNotificationModal('<span class="action_validation">','<span id="id_span"></span>', '<span id="result_span"></span>');?>
	</div>

		<?php print $ui->standardizedThemeJS(); ?>
        <!-- wizard -->
		<script src="js/easyWizard.js" type="text/javascript"></script>
	    <!-- bootstrap color picker -->
		<script src="adminlte/colorpicker/bootstrap-colorpicker.min.js"></script>

 		<script type="text/javascript">
			$(document).ready(function() {

			// loads the fixed action button
				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});

			//loads colorpicker
    		$(".colorpicker").colorpicker();

			//loads datatable functions
				$('#table_ingroup').dataTable();
				$('#table_ivr').dataTable();
				$('#table_did').dataTable();

			//reloads page when modal closes
				$('#add_ingroups').on('hidden.bs.modal', function () {
					window.location = window.location.href;
				});

				$('#add_ivr').on('hidden.bs.modal', function () {
					window.location = window.location.href;
				});

				$('#add_phonenumbers').on('hidden.bs.modal', function () {
					window.location = window.location.href;
				});
			//-----------
				
				$('#route').on('change', function() {
					//  alert( this.value ); // or $(this).val()
					if(this.value == "AGENT") {
					  $('#form_route_agent').show();
					  
					  $('#form_route_ingroup').hide();
					  $('#form_route_phone').hide();
					  $('#form_route_callmenu').hide();
					  $('#form_route_voicemail').hide();
					  $('#form_route_exten').hide();
					}if(this.value == "IN_GROUP") {
					  $('#form_route_ingroup').show();
					  
					  $('#form_route_agent').hide();
					  $('#form_route_phone').hide();
					  $('#form_route_callmenu').hide();
					  $('#form_route_voicemail').hide();
					  $('#form_route_exten').hide();
					}if(this.value == "PHONE") {
					  $('#form_route_phone').show();
					  
					  $('#form_route_agent').hide();
					  $('#form_route_ingroup').hide();
					  $('#form_route_callmenu').hide();
					  $('#form_route_voicemail').hide();
					  $('#form_route_exten').hide();
					}if(this.value == "CALLMENU") {
					  $('#form_route_callmenu').show();
					  
					  $('#form_route_agent').hide();
					  $('#form_route_ingroup').hide();
					  $('#form_route_phone').hide();
					  $('#form_route_voicemail').hide();
					  $('#form_route_exten').hide();
					}if(this.value == "VOICEMAIL") {
					  $('#form_route_voicemail').show();
					  
					  $('#form_route_agent').hide();
					  $('#form_route_ingroup').hide();
					  $('#form_route_phone').hide();
					  $('#form_route_callmenu').hide();
					  $('#form_route_exten').hide();
					}if(this.value == "EXTEN") {
					  $('#form_route_exten').show();
					  
					  $('#form_route_agent').hide();
					  $('#form_route_ingroup').hide();
					  $('#form_route_phone').hide();
					  $('#form_route_voicemail').hide();
					  $('#form_route_callmenu').hide();
					}
					
				});
	
				// for easy wizard -
				$("#add_ingroups").wizard();
				$("#add_phonenumbers").wizard();
				$("#add_ivr").wizard({
					onfinish:function(){
						console.log("Call Menu Added!");

						$.ajax({
							url: "./php/AddTelephonyIVR.php",
							type: 'POST',
							data: $("#create_ivr").serialize(),
							success: function(data) {
							  // console.log(data);
								  if(data == "success"){
									  swal("Success!", "IVR Successfully Created!", "success");
								  }
								  else{
									  sweetAlert("Oops...", "Something went wrong!"+data, "error");
								  }
							}
						});
					}
				});

				

				// ajax commands for modals -
				$('#submit_ingroup').click(function(){
					$('#submit_ingroup').val("Saving, Please Wait.....");
					$('#submit_ingroup').prop("disabled", true);

					var validate_ingroup = 0;
					var groupid = $("#groupid").val();
					var groupname = $("#groupname").val();
					var web_form = $("#web_form").val();
					
					if(groupid == ""){
						validate_ingroup = 1;
					}

					if(groupname == ""){
						validate_ingroup = 1;
					}

					if(web_form == ""){
						validate_ingroup = 1;
					}

					if(validate_ingroup == 0){
					//alert("Validated !");
					
						$.ajax({
							url: "./php/AddTelephonyIngroup.php",
							type: 'POST',
							data: $("#create_ingroup").serialize(),
							success: function(data) {
							  // console.log(data);
								  if(data == "success"){
										swal("Success!", "Ingroup Successfully Created!", "success");
								  		window.setTimeout(function(){location.reload()},1000);
								  		$('#submit_ingroup').val("Submit");
										$('#submit_ingroup').prop("disabled", false);
								  }
								  else{
									  sweetAlert("Oops...", "Something went wrong! "+data, "error");
									  $('#submit_ingroup').val("Submit");
									  $('#submit_ingroup').prop("disabled", false);
								  }
							}
						});
					
					}else{
						sweetAlert("Oops...", "Something went wrong!", "error");
						validate_ingroup = 0;
						$('#submit_ingroup').val("Submit");
						$('#submit_ingroup').prop("disabled", false);
					}
				});
				
				$('#submit_did').click(function(){

				$('#submit_did').val("Saving, Please Wait.....");
				$('#submit_did').prop("disabled", true);

				var validate_did = 0;

				var did_exten = $("#did_exten").val();
				var desc = $("#desc").val();

				var route = $("#route").val();
				var route_exten = $("#route_exten").val();
				var route_exten_context = $("#route_exten_context").val();

					if(did_exten == ""){
						validate_did = 1;
					}
					if(desc == ""){
						validate_did = 1;
					}

					if(route == "EXTEN"){
						if(route_exten == ""){
							validate_did = 1;
						}
						if(route_exten_context == ""){
							validate_did = 1;
						}
					}

					if(validate_did == 0){
						$.ajax({
							url: "./php/AddTelephonyPhonenumber.php",
							type: 'POST',
							data: $("#create_phonenumber").serialize(),
							success: function(data) {
							  // console.log(data);
								  if(data == 1){
										swal("Success!", "Phone Number Successfully Created!", "success");
								  		window.setTimeout(function(){location.reload()},1000)
								  		$('#submit_did').val("Submit");
										$('#submit_did').prop("disabled", false);
								  }else{
										sweetAlert("Oops...", "Something went wrong! "+data, "error");
										$('#submit_did').val("Submit");
										$('#submit_did').prop("disabled", false);
								  }
							}
						});
					}else{
						sweetAlert("Oops...", "Something went wrong!", "error");
						validate_did = 0;
						$('#submit_did').val("Submit");
						$('#submit_did').prop("disabled", false);
					}

				});

			/****
			 ** EDIT ACTIONS
			 ****/

				//EDIT INGROUP
				 $(".edit-ingroup").click(function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="groupid" value="' + $(this).attr('data-id') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				 //EDIT IVR
				 $(".edit-ivr").click(function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="ivr" value="' + $(this).attr('data-id') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				 //EDIT PHONENUMBER/DID
				 $(document).on('click','.edit-phonenumber',function() {
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="did" value="' + $(this).attr('data-id') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });


			/****
			** DELETE Functions
			*****/

			//DELETE INGROUPS
				 $(document).on('click','.delete-ingroup',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                	title: "Are you sure?",   
	                	text: "This action cannot be undone.",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Yes, delete this inbound!",   
	                	cancelButtonText: "No, cancel please!",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteTelephonyInbound.php",
									type: 'POST',
									data: { 
										groupid:id,
									},
									success: function(data) {
									console.log(data);
								  		if(data == 1){
								  			swal("Success!", "Inbound Successfully Deleted!", "success");
											window.setTimeout(function(){location.reload()},3000)
										}else{
											sweetAlert("Oops...", "Something went wrong! "+data, "error");
										}
									}
								});
							} else {     
		                			swal("Cancelled", "No action has been done :)", "error");   
		                	} 
	                	}
	                );
				 });
			//DELETE IVR
				 $(document).on('click','.delete-ivr',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                	title: "Are you sure?",   
	                	text: "This action cannot be undone.",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Yes, delete this IVR!",   
	                	cancelButtonText: "No, cancel please!",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteTelephonyInbound.php",
									type: 'POST',
									data: { 
										ivr:id,
									},
									success: function(data) {
									console.log(data);
								  		if(data == 1){
								  			swal("Success!", "IVR Successfully Deleted!", "success");
											//window.setTimeout(function(){location.reload()},3000)
										}else{
											sweetAlert("Oops...", "Something went wrong! "+data, "error");
										}
									}
								});
							} else {     
		                			swal("Cancelled", "No action has been done :)", "error");   
		                	} 
	                	}
	                );
				 });
			//DELETE PHONENUMBER/DID
				 $(document).on('click','.delete-phonenumber',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                	title: "Are you sure?",   
	                	text: "This action cannot be undone.",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Yes, delete this phonenumber!",   
	                	cancelButtonText: "No, cancel please!",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteTelephonyInbound.php",
									type: 'POST',
									data: { 
										modify_did:id,
									},
									
									success: function(data) {
									//console.log(modify_did);
									console.log(data);
								  		if(data == 1){
								  			swal("Success!", "Phonenumber Successfully Deleted!", "success");
											//window.setTimeout(function(){location.reload()},3000)
										}else{
											sweetAlert("Oops...", "Something went wrong! "+data, "error");
										}
									}
								});
	                		} else {     
		                			swal("Cancelled", "No action has been done :)", "error");   
		                	} 
	                	}
	                );
				 });

				$('.add-option').click(function(){
					var toClone = $('.to-clone-opt').clone();

					toClone.removeClass('to-clone-opt');
					toClone.find('label.control-label').text('');
					toClone.find('.btn-remove').append('<span class="fa fa-remove fa-2x text-red remove-row"></span>');

					$('.cloning-area').append(toClone);
				});

				$(document).on('click', '.remove-row', function(){
					var row = $(this).parent().parent();
					
					row.remove();
				});

				
			});
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
