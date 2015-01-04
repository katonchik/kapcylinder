<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");
	include("include/header_admin.php");

	if($access_granted)
	{
	
	
		$query = "SELECT milestone FROM tournament WHERE tournament_id = $tournid";
		$result = $mysqli->query($query);
		$row = $result->fetch_assoc();
		$milestone = $row['milestone'];
	
?>
    <div id='jqxWidget'>
        <div id="container" style="float: left">
            <div id="jqxSlider" data-milestone="<?php echo $milestone; ?>"></div>
            <br />
            <div id="events" style="border-width: 0px;">
            </div>
<?php } ?>			
			
			<div id="explanation" style="border-top: 1px solid gray; padding-top: 10px;">
				<p>Depending on the milestone you select, the front end will work in a different mode, including
				registration, login, and player home page options.</p>
				<div id="milestone_0">
					<h3>0. Publish Announcement:</h3>
					<p>At this stage, only information pages are available. Players can't register or log in.</p>
				</div>
				<div id="milestone_1">
					<h3>1. Vote for Dates:</h3>
					<p>If you select this milestone, the login and register options become active.
					On player homes, the only available tournament related option will be the calendar 
					where they can specify the dates they will be available to participate in the tournament.</p>
					<p>If you don't want to offer the Vote for Dates option, just skip this milestone.</p>
				</div>
				<div id="milestone_2">
					<h3>2. Open Registration:</h3>
					<p>Once you select this milestone, players can log in to specify their participation 
					and select available tournament offerings and preferences on their home pages.
					The Vote for Dates option becomes unavailable.</p>
				</div>
				<div id="milestone_3">
					<h3>3. Close Registration:</h3>
					<p>Once you reach this milestone, new registrations will fall into the waiting list,
					and you can use them to replace players who fail to come.
					On their home pages, players will see their team names, colors and the list of team fellows.
					Use the following steps:<br />
					1. Select this milestone to close registration<br />
					2. Create teams (in case of manual lotting)<br />
					3. Enable the Teams page to make teams public.</p>
				</div>
				<div id="milestone_4">
					<h3>4. Show Results:</h3>
					<p>Once you select this milestone, the announcement page will be replaced with the results page.
					The registration and login page becomes unavailable again.</p>
					<p><i>Please make sure the results page is marked as <b>NOT active</b> 
					and goes right after the announcement page in the page tree.</i></p>
				</div>
				
				<!--<div id="milestone_">
					<h3>. Show Baskets:</h3>
					<p>Closer to the end of the registration period, you can distribute players into baskets.
					Once you select this milestone, the Baskets page becomes available in the front end.</p>
					<p>If you don't want to make baskets public at this stage, just skip this milestone.</p>
				</div>
				-->
			</div>
        </div>     
    </div>
<?php
include("include/footer_admin.php");
?>