<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	require_once(dirname(__FILE__) . '/tree/class.db.php');
	require_once(dirname(__FILE__) . '/tree/class.tree.php');

	$page_props = array();
	if(isset($_GET['operation'])) {
			$fs = new tree(db::get("mysqli://$db_username:$db_password@$host/$db_name"), array('structure_table' => 'tree_struct', 'data_table' => 'tree_data', 'data' => array('nm','page_url','page_content','is_editable','is_url_editable','layout','defaults_to','is_active')));
		try {
			$rslt = null;
			switch($_GET['operation']) {
				case 'analyze':
					var_dump($fs->analyze(true));
					die();
					break;
				case 'get_node':
					$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
					$temp = $fs->get_children($node);
					$rslt = array();
					foreach($temp as $v) {
						$rslt[] = array('id' => $v['id'], 'text' => $v['nm'], 'children' => ($v['rgt'] - $v['lft'] > 1));
					}
					break;
				case "get_content":
					$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : 0;
					$node = explode(':', $node);
					if(count($node) > 1) {
						$rslt = array('content' => 'Multiple selected');
					}
					else {
						$temp = $fs->get_node((int)$node[0], array('with_path' => true));
						//Display page form
						//$page_props = $temp;
						$content = array(
							'pageid' => $temp['id'], 
							'pagename' => $temp['nm'], 
							'pageurl'=>$temp['page_url'], 
							'pagecontent'=>$temp['page_content'], 
							'is_editable'=>$temp['is_editable'], 
							'is_url_editable'=>$temp['is_url_editable'], 
							'layout'=>$temp['layout'], 
							'is_active'=>$temp['is_active']);
								
						//$rslt = array('content' => 'Selected: /' . implode('/',array_map(function ($v) { return $v['nm']; }, $temp['path'])). '/'.$temp['nm']);
						$rslt = array('content' => $content);
					}
					break;
				case 'create_node':
					$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
					$temp = $fs->mk($node, isset($_GET['position']) ? (int)$_GET['position'] : 0, array('nm' => isset($_GET['text']) ? $_GET['text'] : 'New node'));
					$rslt = array('id' => $temp);
					break;
				case 'rename_node':
					$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
					$rslt = $fs->rn($node, array('nm' => isset($_GET['text']) ? $_GET['text'] : 'Renamed node'));
					break;
				case 'delete_node':
					$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
					$rslt = $fs->rm($node);
					break;
				case 'move_node':
					$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
					$parn = isset($_GET['parent']) && $_GET['parent'] !== '#' ? (int)$_GET['parent'] : 0;
					$rslt = $fs->mv($node, $parn, isset($_GET['position']) ? (int)$_GET['position'] : 0);
					break;
				case 'copy_node':
					$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
					$parn = isset($_GET['parent']) && $_GET['parent'] !== '#' ? (int)$_GET['parent'] : 0;
					$rslt = $fs->cp($node, $parn, isset($_GET['position']) ? (int)$_GET['position'] : 0);
					break;
				case 'save_page':
					$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
					$rslt = $fs->rn($node, array(
						'nm' => $_GET['pagename'], 
						'page_url' => $_GET['pageurl'],
						'page_content' => db::escape($_GET['pagecontent'])));
					break;
				default:
					throw new Exception('Unsupported operation: ' . $_GET['operation']);
					break;
			}
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($rslt);
		}
		catch (Exception $e) {
			header($_SERVER["SERVER_PROTOCOL"] . ' 500 Server Error');
			header('Status:  500 Server Error');
			echo $e->getMessage();
		}
		die();
	}
	include("include/header_admin.php");

?>

	<div id="container" role="main">
			<div id="tree"></div>
			<div id="data">
				<div class="content code" style="display:none;"><textarea id="code" readonly="readonly"></textarea></div>
				<div class="content folder" style="display:none;"></div>
				<div class="content image" style="display:none; position:relative;"><img src="" alt="" style="display:block; position:absolute; left:50%; top:50%; padding:0; max-height:90%; max-width:90%;" /></div>
				<div class="content">
					<div id="resultmsg"></div>
					<input type="hidden" id="pageid" />
					<div class="savepage" style="height:60px;padding-left:650px; margin-bottom: -40px;"><input type="image" src="../_images/submit.png" class="submit" alt="Submit"></div>
					<div id="active" style="height:30px;">
						<input type="checkbox" name="is_active" id="is_active" value="1" /> <label>Is active?</label>
					</div>
					<div id="pgname" style="height:30px;">
						<label>Page name:</label><input type="text" name="pagename" id="pagename" />
					</div>
					<div id="restricted">
					<div id="pgurl" style="height:30px;">
						<label>Page URL id:</label><input type="text" name="pageurl" id="pageurl" />
					</div>
					<div id="pgcontent" style="height:520px;">
						<textarea name="pagecontent" id="pagecontent" style="height: 500px; width: 900px;" /></textarea>
					</div>
					<div style="height:30px;">
						<label>Layout:</label>
						<select name="layout" id="layout">
							<option value="1">1 column</option>
							<option value="2" selected>2 columns</option>
						</select>
					</div>

					</div>
				</div>
			</div>
		</div>

		<script>
		$(function () {
			$(window).resize(function () {
				var h = Math.max($(window).height() - 0, 420);
				$('#container, #data, #tree, #data .content').height(h).filter('.default').css('lineHeight', h + 'px');
			}).resize();

			
				//Begin page submit
				$('div.savepage input.submit').click(function(){
					var $currentLink = $(this);
					var pageid = document.getElementById('pageid').value;
					var pagename = document.getElementById('pagename').value;
					var pageurl = document.getElementById('pageurl').value;
					var layout = document.getElementById('layout').value;
					var is_active = document.getElementById('is_active').checked ? "1" : "0";

					var ed = tinyMCE.get("pagecontent");
					var pagecontent = ed.getContent();

					var resultmsg = document.getElementById('resultmsg');

					$.post("savepage_ajax.php",
					{
						'pageid': pageid,
						'pagename' : pagename,
						'pageurl': pageurl,
						'pagecontent' : pagecontent,
						'layout' : layout,
						'is_active' : is_active
					},
					function(data,status){
						data = $.parseJSON(data);
						if(status == "success")
						{
							resultmsg.innerHTML = data.msg;
							resultmsg.style.color = "#4F8A10";
							$('#tree').jstree('refresh');
						}
						else
						{
							resultmsg.innerHTML = "Error: " + data.msg;
							resultmsg.style.color = "#D8000C";
						}
					});

					return false;
				});
				//End page submit
			
			
			
			
			$('#tree')
				.jstree({
					'core' : {
						'data' : {
							'url' : '?operation=get_node',
							'data' : function (node) {
								return { 'id' : node.id };
							}
						},
						'check_callback' : true,
						'themes' : {
							'responsive' : false
						}
					},
					'plugins' : ['state','dnd','contextmenu','wholerow']
				})
				.on('delete_node.jstree', function (e, data) {
					$.get('?operation=delete_node', { 'id' : data.node.id })
						.fail(function () {
							data.instance.refresh();
						});
				})
				.on('create_node.jstree', function (e, data) {
					$.get('?operation=create_node', { 'id' : data.node.parent, 'position' : data.position, 'text' : data.node.text })
						.done(function (d) {
							data.instance.set_id(data.node, d.id);
						})
						.fail(function () {
							data.instance.refresh();
						});
				})
				.on('rename_node.jstree', function (e, data) {
					$.get('?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
						.fail(function () {
							data.instance.refresh();
						});
				})
				.on('move_node.jstree', function (e, data) {
					$.get('?operation=move_node', { 'id' : data.node.id, 'parent' : data.parent, 'position' : data.position })
						.fail(function () {
							data.instance.refresh();
						});
				})
				.on('copy_node.jstree', function (e, data) {
					$.get('?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent, 'position' : data.position })
						.always(function () {
							data.instance.refresh();
						});
				})
				.on('changed.jstree', function (e, data) {
					if(data && data.selected && data.selected.length) {
						$.get('?operation=get_content&id=' + data.selected.join(':'), function (d) {
							document.getElementById('pageid').value = d.content.pageid;
							document.getElementById('active').style.display = d.content.pageid > 2 ? 'block' : 'none';
							document.getElementById('pgname').style.display = d.content.pageid > 1 ? 'block' : 'none';
							document.getElementById('pagename').value = d.content.pagename;
							document.getElementById('pageurl').value = d.content.pageurl;
							document.getElementById('pagecontent').value = d.content.pagecontent;
							document.getElementById('pgurl').style.display = d.content.is_url_editable ? 'block' : 'none';
							document.getElementById('restricted').style.display = d.content.is_editable ? 'block' : 'none';
							document.getElementById('is_active').checked = d.content.is_active ? true : false;
							document.getElementById('layout').value = d.content.layout;
							document.getElementById('resultmsg').innerHTML = "";
							
							var ed = tinyMCE.get("pagecontent");
							
							ed.setProgressState(0); // Show progress
							window.setTimeout(function() {
								ed.setProgressState(0); // Hide progress
								ed.setContent(d.content.pagecontent);
							}, 500);
							
							//$('#data .default').html(d.content).show();


						});
					}
					else {
							//document.getElementById('pgname').value = data.node.text;
							//alert(data.name);
						//$('#data .content').hide();
						//$('#data .default').html('Select a file from the tree.').show();
					}
				});
				

		});
		</script>
<?php include("include/footer_admin.php"); ?>