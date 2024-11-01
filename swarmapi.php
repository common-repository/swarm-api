<?php
/*
Plugin Name: Swarm API
Plugin URI: http://swarmapi.pbworks.com/WordPress-Plugin
Description: Plugin is used for pulling swarm data. "Swarm data" can currently be defined as tweets, links, members, and photos from a swarm. <a href="http://www.swarmforce.com/about/swarms.php" target="_blank">Read more on swarms.</a> This data can be easily accessed outside of Word Press. You can use the REST interface for the Swarm API directly. See <a href="http://swarmapi.pbworks.com" target="_blank">swarmapi.pbworks.com </a> for documentation and code samples.
Author: Swarmforce
Version: 1.0
Author URI: http://www.swarmforce.com
*/

class SwarmAPI{

	//////////////////////////////////////////////////////////////////////////////
	// declare variables
	//////////////////////////////////////////////////////////////////////////////
	var $version=1.0; //version of word press plugin
	var $host="api.swarmforce.com";
	
	//////////////////////////////////////////////////////////////////////////////
	// constructor
	//////////////////////////////////////////////////////////////////////////////
	function SwarmAPI(){
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// attach menu item to settings
	//////////////////////////////////////////////////////////////////////////////
	function add_swarmapi_menu() {
	  add_options_page('Swarm API Plugin Options', 'Swarm API', 8, 'swarmforce', array(&$this, 'swarmapi_plugin_options'));
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// add css to header
	//////////////////////////////////////////////////////////////////////////////
	function add_swarmapi_header_code() {
		//add admin css
		echo '<link type="text/css" rel="stylesheet" href="' . plugins_url('swarmforce/css/swarmapiadmin.css') . '" />' . "\n";
		//add custom css if set
		if((get_option('sf_custom_css'))){
			echo '<link type="text/css" rel="stylesheet" href="' . plugins_url(get_option('sf_custom_css')) . '" />' . "\n";
		}
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// plugin options
	//////////////////////////////////////////////////////////////////////////////
	function swarmapi_plugin_options() {
	
		//Check for all initial values and set if not there
		if(!(get_option('sf_show_headers'))){
			add_option("sf_show_headers", 1);
		}
		if(!(get_option('sf_show_subheaders'))){
			add_option("sf_show_subheaders", 1);
		}
		if(!(get_option('sf_tweets_num'))){
			add_option("sf_tweets_num", 12);
		}
		if(!(get_option('sf_tweets_order'))){
			add_option("sf_tweets_order", 1);
		}
		if(!(get_option('sf_photos_num'))){
			add_option("sf_photos_num", 12);
		}
		if(!(get_option('sf_photos_order'))){
			add_option("sf_photos_order", 1);
		}
		if(!(get_option('sf_links_num'))){
			add_option("sf_links_num", 5);
		}
		if(!(get_option('sf_links_order'))){
			add_option("sf_links_order", 1);
		}
		if(!(get_option('sf_members_num'))){
			add_option("sf_members_num", 10);
		}
		if(!(get_option('sf_custom_css'))){
			add_option("sf_custom_css", 'swarmforce/css/template.css');
		}
		
		echo $this->swarmapi_option_frm();
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// plugin html form generation
	//////////////////////////////////////////////////////////////////////////////
	function swarmapi_option_frm(){
		
		$output="";
		$output.='<div class="wrap">';	
		
		
		if($_REQUEST['action'] == 'save'){
			$output.='	<div id="message" class="updated fade">
							<p><strong>Options Saved!</strong></p>
						</div>';
		}
		
		$output.='	<div align="left">
        			<h3>Swarm API Optional Settings</h3><BR>
					<div class="sf_option_sm_gray">Need Help? <a href="http://swarmapi.pbworks.com/WordPress-Plugin" target="_blank">Watch a brief tutorial.</a></div><BR><BR>
					<form method="post" action="options.php">'.wp_nonce_field('update-options');
					 
		$output.='	<strong>General</strong>
					<hr class="swarm_hr">
					<table class="form-table">
					<tbody>';
					
		if((get_option('sf_swarm_id'))){
		
		//get swarm info
		$swarm=$this->GetSwarm();
		
		$output.='		<tr valign="top">
							<th scope="row">
								<div>Your Current Swarm:</div>
							</th>
							<td>
								<div class="sf_adminCurrentSwarmImage"><img src="http://'.$this->host.'/v1.0/_external/getphoto.php?w=80&h=80&path='.$swarm->imagethumb.'" alt="'.$swarm->name.'" title="'.$swarm->name.'" /></div>
								<div class="sf_adminCurrentSwarmName"><a href="'.$swarm->link.'" target="_blank">'.$swarm->name.'</a></div>
							</td> 
						</tr>';		
		}
		$output.='	<tr valign="top"><th scope="row"><BR></tr><td></td></tr>
					<tr valign="top">
						<th scope="row">
							<div>Search for Your Swarm:</div>
                            <BR />
							<div class="sf_option_sm_gray">Try typing in the first couple of letters of your swarm\'s name.</div>
						<BR><BR>
                            <div class="sf_option_sm"><a href="http://www.swarmforce.com/swarms/" target="_blank">view all swarms</a>&nbsp;|&nbsp;<a href="http://www.swarmforce.com/create/swarm/" target="_blank">create a new swarm</a></div>
						</th>
						<td>
							<input type="text" name="keywords" id="keywords" />
							<input type="button" name="searchswarms" id="searchswarms" value="Search" />
							<input type="hidden" name="sf_swarm_id" id="sf_swarm_id" value="'.get_option('sf_swarm_id').'"/>
						
						</td>
					</tr>
					<tr valign="top">
					<th scope="row"></th>
					<td>
						<div id="sf_adminswarmIcons" class="sf_adminswarmIcons"></div>
					</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<div>Consumer Key:</div>
                            <BR />
                            <div  class="sf_option_sm"><a href="http://swarmapi.pbworks.com/Rate-Limits" target="_blank">what is this?</a>&nbsp;|&nbsp;<a href="http://'.$this->host.'/request/" target="_blank">request a consumer key</a></div>
						</th>
						<td>
							<input type="text" name="sf_consumer_key" id="sf_consumer_key" value="'.get_option('sf_consumer_key').'" style="padding: 3px; width: 180px;" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<div>Custom CSS:</div>
                            <BR />
                            <div  class="sf_option_sm_gray">This is the path, relative to the plugin directory in Word Press, where your CSS file is located.</div>
						</th>
						<td>
							<input type="text" name="sf_custom_css" id="sf_custom_css" value="'.get_option('sf_custom_css').'" style="padding: 3px; width: 250px;" />
						</td>
					</tr>
                    <tr valign="top">
                    	<th scope="row">
							<div for="commercial_only">Show Module Headers: </div>
						</th>
						<td>
							<input name="sf_show_headers" type="checkbox" id="sf_show_headers" value="1" ';
							
					if(get_option('sf_show_headers')== '1'){ $output.= 'checked="checked"';}
					
		$output.='	/>
						</td>
                     </tr>
                    <tr valign="top">
                    	<th scope="row">
							<div for="commercial_only">Show Module Subheaders: </div>
						</th>
						<td>
							<input name="sf_show_subheaders" type="checkbox" id="sf_show_subheaders" value="1" ';
					if(get_option('sf_show_subheaders')== '1'){$output.= 'checked="checked" ';} 
       	$output.='	/>
						</td>
						 </tr>
					</tbody>
					</table>
					
					<br /><br /><br /><strong>Tweet Module</strong>
					<hr class="swarm_hr">
					<table class="form-table">
					<tbody>
						 <tr valign="top">
							<th scope="row">
								<div>Number of Tweets to Display:</div>
							</th>
						   <td>
							 <input type="text" name="sf_tweets_num" id="sf_tweets_num" value="'.get_option('sf_tweets_num').'" style="padding: 3px; width: 80px;" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<div>Order Tweets By:</div>
							</th>
						   <td>
							<select name="sf_tweets_order" id="sf_tweets_order">
								<option value="1"';
							if(get_option('sf_tweets_order')== '1'){$output.= ' selected';}
		$output.='	>Chronologically</option>
								<option value="2"';
							if(get_option('sf_tweets_order')== '2'){$output.= ' selected';}
		$output.='	>Karma</option>
							</select>
							</td>
						</tr>
							
					</tbody>
					</table>
					<br /><br /><br /><strong>Photo Module</strong>
					<hr class="swarm_hr">
					<table class="form-table">
					<tbody>
						 <tr valign="top">
							<th scope="row">
								<div>Number of Preview Photos to Display:</div>
							</th>
						   <td>
							 <input type="text" name="sf_photos_num" id="sf_photos_num" value="'.get_option('sf_photos_num').'" style="padding: 3px; width: 80px;" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<div>Order Photos By:</div>
							</th>
						   <td>
							<select name="sf_photos_order" id="sf_photos_order">
								<option value="1"';
							if(get_option('sf_photos_order')== '1'){$output.= ' selected';}
		$output.='	>Chronologically</option>
								<option value="2"';
							if(get_option('sf_photos_order')== '2'){$output.= ' selected';}
		$output.='	>Karma</option>
							</select>
							</td>
						</tr>
							
					</tbody>
					</table>
					<br /><br /><br /><strong>Links Module</strong>
					<hr class="swarm_hr">
					<table class="form-table">
					<tbody>
						 <tr valign="top">
							<th scope="row">
								<div>Number of Links to Display:</div>
							</th>
						   <td>
							 <input type="text" name="sf_links_num" id="sf_links_num" value="'.get_option('sf_links_num').'" style="padding: 3px; width: 80px;" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<div>Order Links By:</div>
							</th>
						   <td>
							<select name="sf_links_order" id="sf_links_order">
								<option value="1"';
							if(get_option('sf_links_order')== '1'){$output.= ' selected';}
		$output.='	>Chronologically</option>
								<option value="2"';
							if(get_option('sf_links_order')== '2'){$output.= ' selected';}
		$output.='	>Karma</option>
							</select>
							</td>
						</tr>
							
					</tbody>
					</table>
					<br /><br /><br /><strong>Members Module</strong>
					<hr class="swarm_hr">
					<table class="form-table">
					<tbody>
						 <tr valign="top">
							<th scope="row">
								<div>Number of Members to Display:</div>
							</th>
						   <td>
							 <input type="text" name="sf_members_num" id="sf_members_num" value="'.get_option('sf_members_num').'" style="padding: 3px; width: 80px;" />
							</td>
						</tr>	
					</tbody>
					</table>
					<BR><BR>
					<p class="submit">
					<input type="submit" name="Submit" value="Save Settings &raquo;" style="font-size: 1.5em;" />
					</p>
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="page_options" value="sf_swarm_id,sf_consumer_key,sf_show_headers,sf_show_subheaders,sf_tweets_num,sf_tweets_order,sf_photos_num,sf_photos_order,sf_links_num,sf_links_order,sf_members_num,sf_custom_css" />
					</form>
					<div class="sf_adminfooter"><a href="http://www.swarmforce.com"><img src="http://www.swarmforce.com/_images/logo_white_sm.png" alt="Swarmforce" title="Swarmforce"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://swarmapi.pbworks.com/">API</a>&nbsp;|&nbsp;<a href="http://blog.swarmforce.com">Blog</a>&nbsp;|&nbsp;<a href="https://www.swarmforce.com/terms.php">Terms & Conditions</a>&nbsp;|&nbsp;<a href="https://www.swarmforce.com/privacy.php">Privacy</a></div>
					</div>
					</div>';
					
					
			//output JQUERY
			$output.='<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
			 <script type="text/javascript">                        
						$(document).ready(function(){
						
							var curSwarmId=0;
							
							$("#searchswarms").click(function() {
								var keywords=$(\'input[name=keywords]\').val();
								var url="http://'.$this->host.'/v1.0/GetSwarmByKeywords/?keywords="+keywords+"&jsoncallback=?";
								$.getJSON(url, function(data){
										var output="";
									   $.each(data.items, function(i,item){
											output+="<div class=\'sf_swarmIcon\' swarm_id=\'"+item.id+"\'>";
											output+="<img src=\'http://'.$this->host.'/v1.0/_external/getphoto.php?w=80&h=80&path="+item.imagethumb+"\' alt=\'"+item.name+"\' title=\'"+item.name+"\' />";
											output+="</div>";
          								});
										
										//add spacing
										output+="<BR><BR><BR><BR><BR>";
										//output html
										$("#sf_adminswarmIcons").html(output);
										//now reassign click
										$(".sf_swarmIcon").click(function() {
											$.clickedSwarmIcon($(this).attr(\'swarm_id\'));
										});
								});
							});
							
							$.clickedSwarmIcon=function(id){
								var sf_swarmIconsList = $(".sf_swarmIcon");
								$.each(sf_swarmIconsList, function() {
									 if($(this).attr(\'swarm_id\')==curSwarmId){
										$(this).removeClass("sel");
									 }
									 if($(this).attr(\'swarm_id\')==id){
										$(this).addClass("sel");
									 }
								}); 
								curSwarmId=id;
								$(\'input[name=sf_swarm_id]\').val(curSwarmId);	
							}
							
						 });
						</script>';
					
					
		return $output;
	}
	//////////////////////////////////////////////////////////////////////////////
	// GetSwarmView - for all modules with swarm api data
	//////////////////////////////////////////////////////////////////////////////
	
	function GetSwarm(){
		//set api url
		$url="http://".$this->host."/v1.0/GetSwarm/";
		
		//set arguments
		$args=array("format"=>"JSON");
		if(get_option('sf_swarm_id')){
			$args["swarm_id"]=get_option('sf_swarm_id');
		}
		//set up call
		$session = curl_init();
		curl_setopt ( $session, CURLOPT_URL, $url );
		curl_setopt ( $session, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt ( $session, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $session, CURLOPT_POST, 1);
		
		//get arguments
		if(count($args)>0){
			$params="";
			foreach($args as $key => $value){
				$params.=$key."=".urlencode($value)."&";
			}
			$params=substr($params,0,strlen($params)-1);
			curl_setopt ( $session, CURLOPT_POSTFIELDS, $params);
		}
		
		$result= curl_exec ( $session );
		$data = json_decode($result);
		curl_close( $session );
		
		return $data;
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// GetSwarmView - for all modules with swarm api data
	//////////////////////////////////////////////////////////////////////////////
	
	function GetSwarmView(){
		$allhtml="";
		$allhtml.=$this->GetTweetView();
		$allhtml.=$this->GetPhotoView();
		$allhtml.=$this->GetLinkView();
		$allhtml.=$this->GetMemberView();
		return $allhtml;
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// GetTweetView - for just the tweets module
	//////////////////////////////////////////////////////////////////////////////
	
	function GetTweetView(){
	
		//set api url
		$url="http://".$this->host."/v1.0/GetTweetsBySwarm/";
		
		//set arguments
		$args=array("format"=>"JSON");
		
		if(get_option('sf_tweets_num')){
			$args["count"]=get_option('sf_tweets_num');
		}
		
		if(get_option('sf_swarm_id')){
			$args["swarm_id"]=get_option('sf_swarm_id');
		}
		
		if(get_option('sf_consumer_key')){
			$args["consumer_key"]=get_option('sf_consumer_key');
		}
		
		
		
		//set up call
		$session = curl_init();
		curl_setopt ( $session, CURLOPT_URL, $url );
		curl_setopt ( $session, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt ( $session, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $session, CURLOPT_POST, 1);
		
		//get arguments
		if(count($args)>0){
			$params="";
			foreach($args as $key => $value){
				$params.=$key."=".urlencode($value)."&";
			}
			$params=substr($params,0,strlen($params)-1);
			curl_setopt ( $session, CURLOPT_POSTFIELDS, $params);
		}
		
		$result= curl_exec ( $session );
		$tweetData = json_decode($result);
		$tweets=$tweetData;
		curl_close( $session );
		
		//////////////////////////////////////////////////////////////////////////////
		// make the call for tweets - end
		//////////////////////////////////////////////////////////////////////////////
		
		//now output
		$output= '<div id="sf_tweet_view">';
		if($tweets->error){
			$output.= "An error occured: ".$tweets->error;
		}else{
			if((!empty($tweets)) && (is_array($tweets))){
			
				$output.= "<div id='twitter_header'>";
				if(get_option('sf_show_headers')){
					$output.= "	<div class='moduleheader'>Recent Tweets</div>";
				}
				if(get_option('sf_show_subheaders')){
					$output.= "	<div class='modulesubheader'>These are the most recent tweets in this swarm.</div>";
				}
				$output.= "</div>";
				$alt="";
				foreach($tweets as $t){
					if($alt==""){
						$alt=" wt";
					}else{
						$alt="";
					}
					$tweet=json_decode($t->json);
				$output.= '
					
					<div class="tweet'.$alt.'">
						<div class="tweet_photo">
							<img src="'.$tweet->user->profile_image_url.'" height="48" width="48">
						</div>
						<div class="tweet_msg">
							<div class="tweet_author">
								<a href="http://www.twitter.com/'.$tweet->user->screen_name.'" target="_blank">'.$tweet->user->screen_name.'</a>
							</div>
							<!--
							<div class="tweet_karma">
								('. $t->karma.')
							</div>
							-->
							<div class="tweet_text">
								'. $t->html.' 
								<div class="entry_time">
									<a target="_blank" href="http://www.twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id.'">'.$t->time_ago.'</a> from <span class="entry_type">'.$tweet->source.'</span>
								</div>
							</div>
					
						</div>
					</div>
				  ';
				}//foreach($tweets as $t){
			 }//if(!empty($tweets)){
		}//if($result->error){
		
		$output.= ' </div>';
		
		return $output;
	}
	//////////////////////////////////////////////////////////////////////////////
	// GetPhotoView - for just the photo module
	//////////////////////////////////////////////////////////////////////////////
	function GetPhotoView(){
	
		//set api url
		$url="http://".$this->host."/v1.0/GetPhotosBySwarm/";
		
		//set arguments
		$args=array("format"=>"JSON");
		
		if(get_option('sf_tweets_num')){
			$args["count"]=get_option('sf_photos_num');
		}
		
		if(get_option('sf_swarm_id')){
			$args["swarm_id"]=get_option('sf_swarm_id');
		}
		
		if(get_option('sf_consumer_key')){
			$args["consumer_key"]=get_option('sf_consumer_key');
		}
		
		//set up call
		$session = curl_init();
		curl_setopt ( $session, CURLOPT_URL, $url );
		curl_setopt ( $session, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt ( $session, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $session, CURLOPT_POST, 1);
		
		//get arguments
		if(count($args)>0){
			$params="";
			foreach($args as $key => $value){
				$params.=$key."=".urlencode($value)."&";
			}
			$params=substr($params,0,strlen($params)-1);
			curl_setopt ( $session, CURLOPT_POSTFIELDS, $params);
		}
		
		$result = curl_exec ( $session );
		$photos = json_decode($result);
		curl_close( $session );
		
		
		$output= '<div id="sf_photo_view">';
		if($photos->error){
			$output.= "An error occured: ".$photos->error;
		}else{
			//output HTML and JS for photo viewer
			/////////////////////////////////////////////////////////////////////////////////////
			$output.= "<div id='photo_header'>";
			if(get_option('sf_show_headers')){
				$output.= "	<div class='moduleheader'>Recent Photos</div>";
			}
			if(get_option('sf_show_subheaders')){
				$output.= "	<div class='modulesubheader'>These are the most recent photos in this swarm.</div>";
			}
			$output.= "</div>";
			$output.='<div id="gallery">';
				$output.='<script type="text/javascript" src="http://'.$this->host.'/v1.0/_js/jquery.min.js"></script>';
				$output.='<script type="text/javascript" src="http://'.$this->host.'/v1.0/_js/jquery.galleria.js"></script>';
				$output.='<script type="text/javascript">';
                //JQuery Code
				$output.="
				            $(document).ready(function(){
                            
                            $('.gallery_demo_unstyled').addClass('gallery_demo'); // adds new class name to maintain degradability
                            
                            $('ul.gallery_demo').galleria({
                            history   : false, // activates the history object for bookmarking, back-button etc.
                            clickNext : true, // helper for making the image clickable
                            insert    : '#main_image', // the containing selector for our main image
                            onImage   : function(image,caption,thumb) { // let's add some image effects for demonstration purposes
                            
                            // fade in the image & caption
                            if(! ($.browser.mozilla && navigator.appVersion.indexOf('Win')!=-1) ) { // FF/Win fades large images terribly slow
                            image.css('display','none').fadeIn(1000);
                            }
                            caption.css('display','none').fadeIn(1000);
                            
                            // fetch the thumbnail container
                            var _li = thumb.parents('li');
                            
                            // fade out inactive thumbnail
                            _li.siblings().children('img.selected').fadeTo(500,0.3);
                            
                            // fade in active thumbnail
                            thumb.fadeTo('fast',1).addClass('selected');
                            
                            // add a title for the clickable image
                            image.attr('title','Next image >>');
                            },
                            onThumb : function(thumb) { // thumbnail effects goes here
                            
                            // fetch the thumbnail container
                            var _li = thumb.parents('li');
                            
                            // if thumbnail is active, fade all the way.
                            var _fadeTo = _li.is('.active') ? '1' : '0.6';
                            
                            // fade in the thumbnail when finnished loading
                            thumb.css({display:'none',opacity:_fadeTo}).fadeIn(1500);
                            
                            // hover effects
                            thumb.hover(
                            function() { thumb.fadeTo('fast',1); },
                            function() { _li.not('.active').children('img').fadeTo('fast',0.3); } // don't fade out if the parent is active
                            )
                            }
                            });
                            });";
				//End JQuery Code
				$output.='</script>';
				$output.='<div class="demo">';
					$output.='<ul class="gallery_demo_unstyled">';
					$first=true;
					$curPhoto=1;
					foreach($photos as $p){
						if($p->external!=1){
							$url=$p->server.$p->url;
						}else{
							$url=$p->url;
						}
						
						$encode_path = $url;
						$photoLimit=412;
						if($p->height>$p->width){
							//portrait
							$ratio=$photoLimit/$p->height;
							if($p->height<=$photoLimit){
								$ratio=1;
							}
						}else{
							//landscape
							$ratio=$photoLimit/$p->width;
							if($p->width<=$photoLimit){
								$ratio=1;
							}
						}
						$h=round($p->height*$ratio);
						$w=round($p->width*$ratio);
							
						if($first){
							$first=false;
							$largeimage="http://".$this->host."/v1.0/_external/getphoto.php?path=" . $encode_path . "&h=" . $h ."&w=". $w;
							$output.= ' <li class="active"><img src="'.$largeimage.'" alt="'.$p->title.'" title="'.$p->title.'"></li>';
						}else{
							$largeimage="http://".$this->host."/v1.0/_external/getphoto.php?path=" . $encode_path . "&h=" . $h ."&w=". $w;
							$output.= ' <li><img src="'.$largeimage.'" alt="'.$p->title.'" title="'.$p->title.'"></li>';
						
						}
					}
					$output.='</ul>';
					$output.='<p class="nav"><a href="#" onclick="$.galleria.prev(); return false;">PREVIOUS</a> | <a href="#" onclick="$.galleria.next(); return false;">NEXT</a></p>';
					$output.='<div id="main_image"></div>';
				$output.='</div>';
			$output.='</div>';
			/////////////////////////////////////////////////////////////////////////////////////
	
		
		}
		
		$output.= '</div>';
		//return "<BR><BR>PHOTOS<BR>-------------------------------------<BR><BR>".$result_photos;
		return $output;
		 
	}
	//////////////////////////////////////////////////////////////////////////////
	// GetLinkView - for just the link module
	//////////////////////////////////////////////////////////////////////////////
	
	function GetLinkView(){
		//set api url
		$url="http://".$this->host."/v1.0/GetLinksBySwarm/";
		
		//set arguments
		$args=array("format"=>"JSON");
		
		if(get_option('sf_links_num')){
			$args["count"]=get_option('sf_links_num');
		}
		
		if(get_option('sf_swarm_id')){
			$args["swarm_id"]=get_option('sf_swarm_id');
		}
		
		if(get_option('sf_consumer_key')){
			$args["consumer_key"]=get_option('sf_consumer_key');
		}
		
		
		
		//set up call
		$session = curl_init();
		curl_setopt ( $session, CURLOPT_URL, $url );
		curl_setopt ( $session, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt ( $session, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $session, CURLOPT_POST, 1);
		
		//get arguments
		if(count($args)>0){
			$params="";
			foreach($args as $key => $value){
				$params.=$key."=".urlencode($value)."&";
			}
			$params=substr($params,0,strlen($params)-1);
			curl_setopt ( $session, CURLOPT_POSTFIELDS, $params);
		}
		
		$result= curl_exec ( $session );
		$linkData = json_decode($result);
		$links=$linkData;
		//print_r($links);
		curl_close( $session );
		
		//////////////////////////////////////////////////////////////////////////////
		// make the call for links - end
		//////////////////////////////////////////////////////////////////////////////
		
		//now output
		$output= '<div id="sf_link_view">';
		if($links->error){
			$output.= "An error occured: ".$links->error;
		}else{
			if((!empty($links)) && (is_array($links))){
			
				$output.= "<div id='link_header'>";
				if(get_option('sf_show_headers')){
					$output.= "	<div class='moduleheader'>Recent Links</div>";
				}
				if(get_option('sf_show_subheaders')){
					$output.= "	<div class='modulesubheader'>These are the most recent links in this swarm.</div>";
				}
				$output.= "</div>";
				$alt="";
				foreach($links as $link){
					if($alt==""){
						$alt=" wt";
					}else{
						$alt="";
					}
					$output.= '<div class="link'.$alt.'">
						<div class="linkphoto"><img src="'. $link->img.'"/></div>
						<div class="linkinfo">';
							 
							if(($link->title) && ($link->title!="")){
								$displaytitle=$link->title;
								$limit=80;
								if(strlen($displaytitle)>$limit){
									$displaytitle=substr($displaytitle,0,$limit)." ...";
								}
								
								$output.= '<div class="linktitle">'. $displaytitle.'</div>';
							}
							
							$maxlen=32;
							if(strlen($link->url)>$maxlen){
								$linkdisplay=substr($link->url,0,$maxlen)." ...";
							}else{
								$linkdisplay=$link->url;
							}
							
							 $output.= '<div id="urlheader" class="linkheader">URL:</div>
							 <div class="linkurl"><a href="'. $link->url.'" target="_blank">'. $linkdisplay.'</a></div>';
							 
							 if($link->url!=$link->destination){
								$limit=70;
								if(strlen($link->destination)>$limit){
									$destination=substr(urldecode($link->destination),0,$limit)." ...";
								}else{
									$destination=urldecode($link->destination);
								}
							 
							 	$output.= '<div id="destinationheader" class="linkheader">Destination:</div>
							 	<div class="linkdestination">'. $destination.'</div>';
							 }
							 
							 if($link->hits){
							 		$output.= '<div id="clicksheader" class="linkheader">Click Throughs:</div>
							 		<div class="linkstats">'. number_format($link->hits,0,".",",").'</div>';
							 }
					$output.= '</div>
					</div>
				  ';
				}//foreach($links as $link){
			 }//if(!empty($links)){
		}//if($result->error){
		
		$output.= ' </div>';
		
		return $output;
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// GetLinkView - for just the link module
	//////////////////////////////////////////////////////////////////////////////
	
	function GetMemberView(){
		//set api url
		$url="http://".$this->host."/v1.0/GetMembersBySwarm/";
		
		//set arguments
		$args=array("format"=>"JSON");
		
		if(get_option('sf_members_num')){
			$args["count"]=get_option('sf_members_num');
		}
		
		if(get_option('sf_swarm_id')){
			$args["swarm_id"]=get_option('sf_swarm_id');
		}
		
		if(get_option('sf_consumer_key')){
			$args["consumer_key"]=get_option('sf_consumer_key');
		}
		
		
		//////////////////////////////////////////////////////////////////////////////
		// make the call for members - begin
		//////////////////////////////////////////////////////////////////////////////
		
		//set up call
		$session = curl_init();
		curl_setopt ( $session, CURLOPT_URL, $url );
		curl_setopt ( $session, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt ( $session, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $session, CURLOPT_POST, 1);
		
		//get arguments
		if(count($args)>0){
			$params="";
			foreach($args as $key => $value){
				$params.=$key."=".urlencode($value)."&";
			}
			$params=substr($params,0,strlen($params)-1);
			curl_setopt ( $session, CURLOPT_POSTFIELDS, $params);
		}
		
		$result= curl_exec ( $session );
		$memberData = json_decode($result);
		$members=$memberData;
		curl_close( $session );
		//////////////////////////////////////////////////////////////////////////////
		// make the call for members - end
		//////////////////////////////////////////////////////////////////////////////
		$output= '<div id="sf_member_view">';
		if($members->error){
		   $output.= "An error occured: ".$members->error;
		}else{
			if(!empty($members)){
				$output.= "<div id='member_header'>";
				if(get_option('sf_show_headers')){
					$output.= "	<div class='moduleheader'>Top Contributors</div>";
				}
				if(get_option('sf_show_subheaders')){
					$output.= "	<div class='modulesubheader'>These are the top contributors in this swarm.</div>";
				}
				$output.= "</div>";
				$output.= '<div class="topswarmers"><ul>';
				//<li><a target="_blank" href='http://www.swarmforce.com/profile/?id=182336EQWDbghIOS8ja4'><img src='http://www.swarmforce.com//_shared/includes/getphoto.php?path=%2F_images%2Fuser_generated%2Fusers%2F182336EQWDbghIOS8ja4%2Fmain%2FbvQ8V2N5Jahu.png&h=45&w=45' alt='Brandon Geiger (233.849)' title='Brandon Geiger (233.849)'><div class='mask_avatar' alt='Brandon Geiger (233.849)' title='Brandon Geiger (233.849)'></div></a></li>
				foreach($members as $member){
					$output.= "<li><a target='_blank' href='http://www.swarmforce.com/profile/?id=".$member->public_id."'><img src='http://".$this->host."/v1.0/_external/getphoto.php?path=".$member->photo."&h=45&w=45' alt='".$member->display_name." (".$member->karma.")' title='".$member->display_name." (".$member->karma.")'><div class='mask_avatar' alt='".$member->display_name." (".$member->karma.")' title='".$member->display_name." (".$member->karma.")'></div></a></li>";
				}
				$output.= "</ul></div>";
			}//if(!empty($members)){
		}//if($result->error){
		$output.= '</div>';
		
		return $output;
	}
	//////////////////////////////////////////////////////////////////////////////
	// sf_check_swarm_view - check content of current page for sf tag
	//////////////////////////////////////////////////////////////////////////////
	
	function sf_check_for_sf_tags($content){
		$cContent=$content;
		$identifiers=array("swarmview","tweetview","photoview","linkview","memberview");
		foreach($identifiers as $i){
			$identifier="[sf:".$i."]";
			//echo "<BR>identifier=".$identifier;
			$index=strpos($cContent,$identifier);
				//echo "<BR>index=".$index;
			if($index!==false){
					//echo "<BR>in loop";
				switch ($i) {
					case "swarmview":
						$newcontent=$this->GetSwarmView();
						break;
					case "tweetview":
						$newcontent=$this->GetTweetView();
						break;
					case "photoview":
						$newcontent=$this->GetPhotoView();
						break;
					case "linkview":
						$newcontent=$this->GetLinkView();
						break;
					case "memberview":
						$newcontent=$this->GetMemberView();
						break;
	
				}
				$cContent=str_replace($identifier,$newcontent,$cContent);
				
			}else{
				//don't do anything
			}
		}
		return $cContent;
	}
}






//////////////////////////////////////////////////////////////////////////////
// add_action must be in root
//////////////////////////////////////////////////////////////////////////////
$sfapi=new SwarmAPI();
add_action('the_content', array(&$sfapi, 'sf_check_for_sf_tags'));
add_action('admin_menu', array(&$sfapi, 'add_swarmapi_menu'));
add_action('admin_head', array(&$sfapi, 'add_swarmapi_header_code'));
add_action('wp_head', array(&$sfapi, 'add_swarmapi_header_code'));

    
?>