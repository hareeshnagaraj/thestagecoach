<?php 

require("db_connection.php");
require("functions.php");
require("template_functions.php");

if($type == 'group'){
	$photoid = str_replace('groupPic_','',$photoid);
}
if($type == 'cover'){
    $photoid = str_replace('coverpic_','',$photoid);
}

$photoQuery = pr_mysql_query("SELECT * FROM photos WHERE id < '$photoid' AND emailaddress = '$email'  ORDER BY id DESC LIMIT 20");
$numPhotos = mysql_num_rows($photoQuery);

for($iii = 0; $iii < $numPhotos; $iii++) {

    $userphotosource = mysql_result($photoQuery, $iii, "source");
    $userphotosid = mysql_result($photoQuery, $iii, "id");
    $imageid = mysql_result($photoQuery, $iii, "id");
    $userphotoscaption = mysql_result($photoQuery, $iii, "caption");
    $newsource = str_replace("userphotos/","userphotos/thumbs/", $userphotosource);
    
    $background = getTimThumb($userphotosource,180,180);
    $largepic = getTimThumb($userphotosource,-1,800);

    $id =  mysql_result($photoQuery, $iii, "id");

    if($type == 'cover'){
   		echo'<div id="'.$id.'" class="to_be_Selected_cover" style="background-image:url('.$background.');">';
	   	echo'
	    </div>

	    <script type="text/javascript">
	        $( document ).ready(function(){
	            $("#'.$id.'").click(function(){   


	                setCoverPic("'.$largepic.'");

	            });
	        });

	    </script>
	    ';
    }
    if($type == 'full'){
   		echo'<div id="'.$id.'" class="to_be_Selected_full" style="background-image:url('.$background.');">';
   		echo'
	    </div>

	    <script type="text/javascript">
	        $( document ).ready(function(){
	            $("#'.$id.'").click(function(){   
					addFullPic("'.$largepic.'");
	            });
	        });

	    </script>
	    ';
    }
    if($type == 'group'){
   		echo'
 		
 		<div id="groupPic_'.$id.'" class="to_be_Selected_group" style="background-image:url('.$background.');">

        </div>
        <script type="text/javascript">
            $( document ).ready(function(){
                $("#groupPic_'.$id.'").click(function(){   
                     addFullPic_Group("'.$largepic.'", "#groupPic_'.$id.'");
                });
            });
        </script>



   		';
    }
	

} //end of for loop

?>