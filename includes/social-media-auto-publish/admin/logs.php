<p>Last five logs of each social media account</p>
<table class="widefat auto-share-logs-table">
	<thead>
		<tr class="xyz_smap_log_tr">
			<th scope="col" width="1%"></th>
			<th scope="col" width="12%"><?php esc_html_e( 'Post ID' ); ?></th>
			<th scope="col" width="18%"><?php esc_html_e( 'Account type' ); ?></th>
			<th scope="col" width="18%"><?php esc_html_e( 'Published On' ); ?></th>
			<th scope="col" width="15%"><?php esc_html_e( 'Status' ); ?></th>
		</tr>
	</thead>
	<?php 
	
	
	$post_fb_logsmain = get_option('xyz_smap_fbap_post_logs' );
	$post_tw_logsmain = get_option('xyz_smap_twap_post_logs' );
	//$post_ln_logsmain = get_option('xyz_smap_lnap_post_logs' );
	
	$post_fb_logsmain_array = array();

	if ( $post_fb_logsmain )
	{
		foreach ($post_fb_logsmain as $logkey1 => $logval1)
		{
			$post_fb_logsmain_array[]=$logval1;
		
		}
	}
	
	
	
	if(count($post_fb_logsmain_array))
	{
		for($i=4;$i>=0;$i--)
		{
			if($post_fb_logsmain_array[$i]!='')
			{
				$post_fb_logs=$post_fb_logsmain_array[$i];
		
				$postid=$post_fb_logs['postid'];
			    $acc_type=$post_fb_logs['acc_type'];
				$publishtime=$post_fb_logs['publishtime'];
				if($publishtime!="")
					$publishtime=xyz_smap_local_date_time('Y/m/d g:i:s A',$publishtime);
				$status=$post_fb_logs['status'];
			
			?>
			<tr>
				<td>&nbsp;</td>
				<td  style="vertical-align: middle !important;">
				<?php echo get_the_title($postid);	?>
				</td>
				
				<td  style="vertical-align: middle !important;">
				<?php echo $acc_type;?>
				</td>
				
				<td style="vertical-align: middle !important;">
				<?php echo $publishtime;?>
				</td>
				
				<td style="vertical-align: middle !important;">
				<?php

				
			  if($status=="1")
					echo '<span style="color:green">' . esc_html__( 'Success' ) . '</span>';
				else if($status=="0")
					echo '';
				else
				{
					$arrval=unserialize($status);
					foreach ($arrval as $a=>$b)
						echo "<span style=\"color:red\">".$a." : ".$b."</span><br>";
				
				}
				
				 ?>
				</td>
			</tr>
			<?php  
			}
		}
	}
		
		
	$post_tw_logsmain_array = array();

	if ( $post_tw_logsmain )
	{
		foreach ($post_tw_logsmain as $logkey2 => $logval2)
		{
			$post_tw_logsmain_array[]=$logval2;
		}
	}
		
	if(count($post_tw_logsmain_array))
	{
		for($i=4;$i>=0;$i--)
		{
			if($post_tw_logsmain_array[$i]!='')
			{
				$post_tw_logs=$post_tw_logsmain_array[$i];
				$postid=$post_tw_logs['postid'];
				$acc_type=$post_tw_logs['acc_type'];
				$publishtime=$post_tw_logs['publishtime'];
				if($publishtime!="")
					$publishtime=xyz_smap_local_date_time('Y/m/d g:i:s A',$publishtime);
				$status=$post_tw_logs['status'];
				?>
				<tr>
					<td>&nbsp;</td>
					<td  style="vertical-align: middle !important;">
					<?php echo get_the_title($postid);	?>
					</td>
					
					<td  style="vertical-align: middle !important;">
					<?php echo $acc_type;?>
					</td>
					
					<td style="vertical-align: middle !important;">
					<?php echo $publishtime;?>
					</td>
					
					<td style="vertical-align: middle !important;">
					<?php
					
					
					if($status=="1")
					echo '<span style="color:green">' . esc_html__( 'Success' ) . '</span>';
					else if($status=="0")
					echo '';
					else
					{
					$arrval=unserialize($status);
					foreach ($arrval as $a=>$b)
					echo "<span style=\"color:red\">".$a." : ".$b."</span><br>";
					
					}
					
					?>
					</td>
				</tr>
				<?php  
			}
		}
	}
					

	/*$post_ln_logsmain_array = array();
	foreach ($post_ln_logsmain as $logkey3 => $logval3)
	{
		$post_ln_logsmain_array[]=$logval3;
	
	}
	if(count($post_ln_logsmain_array))
	{
		for($i=4;$i>=0;$i--)
		{
			if($post_ln_logsmain_array[$i]!='')
			{
				$post_ln_logs=$post_ln_logsmain_array[$i];		
				$postid=$post_ln_logs['postid'];
				$acc_type=$post_ln_logs['acc_type'];
				$publishtime=$post_ln_logs['publishtime'];
				if($publishtime!="")
					$publishtime=xyz_smap_local_date_time('Y/m/d g:i:s A',$publishtime);
				$status=$post_ln_logs['status'];
			
				?>
				<tr>
					<td>&nbsp;</td>
					<td  style="vertical-align: middle !important;">
					<?php echo get_the_title($postid);	?>
					</td>
					
					<td  style="vertical-align: middle !important;">
					<?php echo $acc_type;?>
					</td>
					
					<td style="vertical-align: middle !important;">
					<?php echo $publishtime;?>
					</td>
					
					<td style="vertical-align: middle !important;">
					<?php
					
					
					if($status=="1")
					echo "<span style=\"color:green\">Success</span>";
					else if($status=="0")
					echo '';
					else
					{
					$arrval=unserialize($status);
					foreach ($arrval as $a=>$b)
					echo "<span style=\"color:red\">".$a." : ".$b."</span><br>";
					
					}
					
					?>
					</td>
				</tr>
				<?php  
			}
		}
	}*/
	//if($post_fb_logsmain=="" && $post_tw_logsmain=="" && $post_ln_logsmain==""){
	if($post_fb_logsmain=="" && $post_tw_logsmain==""){?>
		<tr><td colspan="5" style="padding: 5px;"><?php esc_html_e( 'No logs found' ); ?></td></tr>
	<?php }?>
	
</table>
