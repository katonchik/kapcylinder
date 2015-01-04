<?php
	include("include/fe_includes.inc.php");
	
	if(isset($_REQUEST['err']) && isset($lang['error' . $_REQUEST['err']]))
	{
		$errormsg = $lang['error' . $_REQUEST['err']];
	}
	
	if($settings['milestone'] == 4)
	{
		$frontPage = "results";
	}
	else
	{
		$frontPage = "announcement";
	}

	$filter="";
	$params_arr = array();
	if(isset($_REQUEST['params']))
	{
		$params_arr=explode("/", $_REQUEST['params']);
		if($params_arr[0] == 'index' || $params_arr[0] == '')
		{
			$page = $frontPage;
		}
		else
		{
			$page = $params_arr[0];
		}
		$page = preg_replace('/\\.[^.\\s]{3,4}$/', '', $page);
		if(isset($params_arr[1]))
		{
			$filter=$params_arr[1];
		}
	}
	else
	{
		$page = $frontPage;
//		$params_arr="";
	}

//echo "DEBUG. $page = " . $page . "<br />";
	
	$page_filename = $page . ".php";
	$file_exists = file_exists($page_filename);

	
	if(($settings['milestone'] == 0 || $settings['milestone'] == 4)
		&& in_array($page, array('home', 'login', 'register', 'forgotpass')))
	{
		$page = $frontPage;
	}
	
	
	
	
	//Begin page data initialization
	require_once('admin/tree/class.db.php');
	require_once('admin/tree/class.tree.php');
	$fs = new tree(db::get("mysqli://$db_username:$db_password@$host/$db_name"), array('structure_table' => 'tree_struct', 'data_table' => 'tree_data', 'data' => array('nm','page_url','page_content','defaults_to','layout','is_active')));
	try {
		$page_data = $fs->get_node_by_page_url($page, array('with_path' => true));
		if(!$page_data['is_active'])
		{
			//Default to the front page 
			$page = $frontPage;
			$page_data = $fs->get_node_by_page_url($page, array('with_path' => true));
		}
	} catch(Exception $e) {
		if($file_exists)
		{
			//A service (system) page
			switch($page)
			{
				case "allnews":
					$page_data['layout'] = 2;
					$page_data['nm'] = 'News';
					break;
				default:	
					$page_data['layout'] = 1;
					break;
			}
		}
		else
		{
			//Hack attack or mistake
			$page = $frontPage;
			$page_data = $fs->get_node_by_page_url($page, array('with_path' => true));
		}
	}
	//End page data initialization

	
	if($page!="login" && $page!="register" && $page!="logout" && $page!="resetpass") //kostyl', nado izmenit architecture.
	{
		include("include/header.php");
	}

	//Begin include content
	if($page==$frontPage)
	{
		echo '<div class="infobox">
			' . $page_data['page_content'] . '
		</div>';
	}
	else
	{
		if(isset($page_data['nm']))
		{
			echo '<h3 id="pageTitle" class="page_title" data-filter="' . $filter . '"  data-page="' . $params_arr[0] . '">' . $page_data['nm'] . '</h3>';
		}
	
		if($file_exists)
		{
			$errormsg = "";
			$successmsg = "";
			include($page_filename);
		}
		else
		{
			echo $page_data['page_content'];
		}
	}
	//End include content

include("include/footer.php");
?>