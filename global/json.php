<?php

include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/mysqlConnection.php");
include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/_misc/encrypt.php");

$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);

function getPopularPeople(){
        global $link;
        //Ids are prefixed with a 'p', and are ordered by most popular
        $a = array();
        $query = "SELECT access, account_id as id, fullname, MAX(nScans) as nScans
                        FROM ((SELECT t.access, i.account_id, t.fullname, COUNT(DISTINCT a.account_ID) as nScans
                            FROM tags t, actions a, invitations i
                            WHERE a.type = 'P'
                            AND a.tag_ID = t.tag_ID
                            AND t.type = 'P'
                            AND i.tag_id = t.tag_id
                            GROUP BY i.account_id, t.fullname)
                        UNION
                            (SELECT t.access, a.account_id, t.fullname, 0
                            FROM accounts a, invitations i, tags t
                            WHERE a.account_id = i.account_id
                            AND t.tag_id = i.tag_id
                            AND t.type = 'P')) as t1 
                        GROUP BY access, fullname
                    ORDER BY nScans DESC";
        
        @mysqli_real_query($link,$query);
	    $result = mysqli_store_result($link);
	    $i=0;
	    if(mysqli_num_rows($result)>0)
	    {
		    $row=mysqli_fetch_assoc($result);
		    do{
		        if($row['access'] == "P"){
			        $a[$i]['nametitle']=$row['fullname'];
			    }
			    else{
			        $a[$i]['nametitle']="Annonymous";
			    }
			    $a[$i]['id']="p".$row['id'];
			    $a[$i]['nScans']=$row['nScans'];
			    $i++;
		    }while($row=mysqli_fetch_assoc($result));
	    }
        $json = addslashes(json_encode($a));
        $fileHandler = fopen("/var/www/vhosts/smart-qr.com/subdomains/grand/html/global/static/grand/popularPeople", 'w');
        fwrite($fileHandler, $json);
        fclose($fileHandler);
    }
    
    function getPopularPosters(){
        global $link;
        //Ids are prefixed with an 'o', and are ordered by most popular
        $a = array();
        $query = "SELECT access, tag_id as id, fullname, MAX(nScans) as nScans
                        FROM ((SELECT t2.access, t2.tag_id, t2.fullname, COUNT(DISTINCT a.account_id) as nScans
                            FROM tags t, tags t2, actions a, invitations i, invitations i2
                            WHERE a.type = 'O'
                            AND i.account_id = a.account_id
                            AND i.scan = '0'
                            AND t.type = 'P'
                            AND t.tag_id = i2.tag_id
                            AND i2.account_id = i.account_id
                            AND a.tag_id = t2.tag_id
                            GROUP BY t2.access, t2.tag_id, t2.fullname)
                        UNION
                        (SELECT t.access, t.tag_id, t.fullname, 0
                            FROM tags t
                            WHERE t.type = 'O')) as t1
                        GROUP BY fullname
                        ORDER BY nScans DESC";
                        
        @mysqli_real_query($link,$query);
	    $result = mysqli_store_result($link);
	    $i=0;
	    if(mysqli_num_rows($result)>0)
	    {
		    $row=mysqli_fetch_assoc($result);
		    do{
		        if($row['access'] == "P"){
			        $a[$i]['nametitle']=$row['fullname'];
			    }
			    else{
			        $a[$i]['nametitle']="Private Poster";
			    }
			    $a[$i]['id']="o".$row['id'];
			    $a[$i]['nScans']=$row['nScans'];
			    $i++;
		    }while($row=mysqli_fetch_assoc($result));
	    }
        $json = addslashes(json_encode($a));
        $fileHandler = fopen("/var/www/vhosts/smart-qr.com/subdomains/grand/html/global/static/grand/popularPosters", 'w');
        fwrite($fileHandler, $json);
        fclose($fileHandler);
    }
    
    function getLikedPosters(){
        global $link;
        //Ids are prefixed with an 'l', and are ordered by most liked
        $a = array();
        $query = "SELECT access, tag_id as id, fullname, MAX(nLike) as nLike
                        FROM ((SELECT t2.access, t2.tag_id, t2.fullname, COUNT(a.rate) as nLike
                            FROM tags t, tags t2, actions a, invitations i, invitations i2
                            WHERE a.type = 'O'
                            AND i.account_id = a.account_id
                            AND i.scan = '0'
                            AND a.rate = '5'
                            AND t.type = 'P'
                            AND t.tag_id = i2.tag_id
                            AND i2.account_id = i.account_id
                            AND a.tag_id = t2.tag_id
                            GROUP BY t2.access, t2.tag_id, t2.fullname)
                        UNION
                        (SELECT t.access, t.tag_id, t.fullname, 0
                            FROM tags t
                            WHERE t.type = 'O')) as t1
                        GROUP BY fullname
                        ORDER BY nLike DESC";
                        
        @mysqli_real_query($link,$query);
	    $result = mysqli_store_result($link);
	    $i=0;
	    if(mysqli_num_rows($result)>0)
	    {
		    $row=mysqli_fetch_assoc($result);
		    do{
		        if($row['access'] == "P"){
			        $a[$i]['nametitle']=$row['fullname'];
			    }
			    else{
			        $a[$i]['nametitle']="Private Poster";
			    }
			    $a[$i]['id']="l".$row['id'];
			    $a[$i]['nScans']=$row['nLike'];
			    $i++;
		    }while($row=mysqli_fetch_assoc($result));
	    }
        $json = addslashes(json_encode($a));
        $fileHandler = fopen("/var/www/vhosts/smart-qr.com/subdomains/grand/html/global/static/grand/likedPosters", 'w');
        fwrite($fileHandler, $json);
        fclose($fileHandler);
    }
    
    function getControversialPosters(){
        global $link;
        //Ids are prefixed with an 'l', and are ordered by most controversial
        $a = array();
        $likes = array();
        $dislikes = array();
        $query = "SELECT t2.access, t2.tag_id as id, t2.fullname, a.rate
                    FROM tags t, tags t2, actions a, invitations i, invitations i2
                    WHERE a.type = 'O'
                    AND i.account_id = a.account_id
                    AND i.scan = '0'
                    AND t.type = 'P'
                    AND t.tag_id = i2.tag_id
                    AND i2.account_id = i.account_id
                    AND a.tag_id = t2.tag_id
                    ORDER BY t2.tag_id";
                        
        @mysqli_real_query($link,$query);
	    $result = mysqli_store_result($link);
	    $i=-1;
	    $tagid = -1;
	    if(mysqli_num_rows($result)>0)
	    {
		    $row=mysqli_fetch_assoc($result);
		    do{
		        if($row['id'] != $tagid){
		            $i++;
		            if($row['access'] == "P"){
			            $a[$i]['nametitle']=$row['fullname'];
			        }
			        else{
			            $a[$i]['nametitle']="Private Poster";
			        }
			        $a[$i]['id']="l".$row['id'];
			        if($row['rate'] == 5){
			            $a[$i]['nScans'] = 1;
			        }
			        else{
			            $a[$i]['nScans'] = -1;
			        }
			        $tagid = $row['id'];
			    }
			    else{
			        if($row['rate'] == 5){
			            $a[$i]['nScans'] += 1;
			        }
			        else{
			            $a[$i]['nScans'] -= 1;
			        }
			    }
		    }while($row=mysqli_fetch_assoc($result));
	    }
	    $finalA = array();
	    $smallestSoFar = 10000;
	    $secondSmallestSoFar = 10000;
	    $thirdSmallestSoFar = 10000;
	    foreach($a as $node){
	        if(abs($node['nScans']) <= $smallestSoFar){
	            @$finalA[2] = $finalA[1];
	            @$finalA[1] = $finalA[0];
	            $finalA[0] = $node;
	            $thirdSmallestSoFar = $secondSmallestSoFar;
	            $secondSmallestSoFar = $smallestSoFar;
	            $smallestSoFar = abs($node['nScans']);
	        }
	        else if(abs($node['nScans']) <= $secondSmallestSoFar){
	            @$finalA[2] = $finalA[1];
	            $finalA[1] = $node;
	            $thirdSmallestSoFar = $secondSmallestSoFar;
	            $secondSmallestSoFar = abs($node['nScans']);
	        }
	        else if(abs($node['nScans']) <= $thirdSmallestSoFar){
	            $finalA[2] = $node;
	            $thirdSmallestSoFar = abs($node['nScans']);
	        }
	    }
        $json = addslashes(json_encode($finalA));
        $fileHandler = fopen("/var/www/vhosts/smart-qr.com/subdomains/grand/html/global/static/grand/controversialPosters", 'w');
        fwrite($fileHandler, $json);
        fclose($fileHandler);
    }
    
    function getGeneralPTable(){
        global $link;
        /*
         * Creates a table with some global statistics about users
         * nScans       := #Times I have been scanned by someone else
         * nOtherScans  := #People I have scanned
         * nScans1      := #Times My Posters have been scanned
         * nOtherScans1 := #Other posters I have scanned
         *
         * Everything has been put into one query for performance reasons.
         */
        
        $a = array();
        $query = "SELECT t.access, i.account_id as id, c1.fullname, c1.nScans, c2.nOtherScans, c3.nScans1, c4.nOtherScans1
                    FROM (SELECT fullname, MAX(nScans) as nScans
                        FROM ((SELECT t.fullname, COUNT(DISTINCT a.account_ID) as nScans
                            FROM tags t, actions a
                            WHERE a.type = 'P'
                            AND a.tag_ID = t.tag_ID
                            AND t.type = 'P'
                            GROUP BY t.fullname)
                        UNION
                            (SELECT t.fullname, 0
                            FROM accounts a, invitations i, tags t
                            WHERE a.account_id = i.account_id
                            AND t.tag_id = i.tag_id
                            AND t.type = 'P')) as t1 
                        GROUP BY fullname) as c1,
                        (SELECT fullname, MAX(nOtherScans) as nOtherScans
                        FROM ((SELECT t.fullname, COUNT(DISTINCT a.tag_ID) as nOtherScans
                            FROM tags t, actions a, invitations i
                            WHERE a.type = 'P'
                            AND i.account_id = a.account_id
                            AND i.tag_id = t.tag_id
                            AND t.type = 'P'
                            GROUP BY t.fullname)
                        UNION
                            (SELECT t.fullname, 0
                            FROM accounts a, invitations i, tags t
                            WHERE a.account_id = i.account_id
                            AND t.tag_id = i.tag_id
                            AND t.type = 'P')) as t2 
                        GROUP BY fullname) as c2,
                        (SELECT fullname, MAX(nScans1) as nScans1
                        FROM ((SELECT t.fullname, COUNT(DISTINCT a.account_id) as nScans1
                            FROM tags t, actions a, invitations i, invitations i2
                            WHERE a.type = 'O'
                            AND a.tag_id = i.tag_id
                            AND i.account_id != a.account_id
                            AND i.scan = '0'
                            AND t.type = 'P'
                            AND t.tag_id = i2.tag_id
                            AND i2.account_id = i.account_id
                            GROUP BY t.fullname)
                        UNION
                            (SELECT t.fullname, 0
                            FROM accounts a, invitations i, tags t
                            WHERE a.account_id = i.account_id
                            AND t.tag_id = i.tag_id
                            AND t.type = 'P')) as t3
                        GROUP BY fullname) as c3,
                        (SELECT fullname, MAX(nOtherScans1) as nOtherScans1
                        FROM ((SELECT t.fullname, COUNT(DISTINCT a.tag_ID ) AS nOtherScans1
                            FROM tags t, actions a, invitations i
                            WHERE a.type = 'O'
                            AND i.account_id = a.account_id
                            AND i.tag_id = t.tag_id
                            AND t.type = 'P'
                            GROUP BY t.fullname)
                        UNION 
                        (SELECT t.fullname, 0
                            FROM accounts a, invitations i, tags t
                            WHERE a.account_id = i.account_id
                            AND t.tag_id = i.tag_id
                            AND t.type = 'P')) AS t4
                        GROUP BY fullname) as c4, invitations i, tags t
                    WHERE c1.fullname = c2.fullname
                    AND c2.fullname = c3.fullname
                    AND c3.fullname = c4.fullname
                    AND c1.fullname = t.fullname
                    AND i.tag_id = t.tag_id
                    ORDER BY c1.nScans DESC, c2.nOtherScans DESC, c3.nScans1 DESC, c4.nOtherScans1 DESC
                    LIMIT 10";
	    @mysqli_real_query($link,$query);
	    $result = mysqli_store_result($link);
	    $i=0;
	    $a = array();
	    if(mysqli_num_rows($result)>0)
	    {
		    $row=mysqli_fetch_assoc($result);
		    do{
		        $a[$i]['id']=encrypt($row['id']);
		        if($row['access'] == "P"){
			        $a[$i]['nametitle']=$row['fullname'];
			    }
			    else{
			        $a[$i]['nametitle']="Annonymous";
			    }
			    $a[$i]['nScans']=$row['nScans'];
			    $a[$i]['nOtherScans']=$row['nOtherScans'];
			    $a[$i]['nScans1']=$row['nScans1'];
			    $a[$i]['nOtherScans1']=$row['nOtherScans1'];
			    $i++;
		    }while($row=mysqli_fetch_assoc($result));
	    }
        $json = addslashes(json_encode($a));
        $fileHandler = fopen("/var/www/vhosts/smart-qr.com/subdomains/grand/html/global/static/grand/generalPTable", 'w');
        fwrite($fileHandler, $json);
        fclose($fileHandler);
    }
    
    function getGeneralOTable(){
        global $link;
        /*
         * Creates a table with some global statistics about users
         * nScans       := #Times that this poster has been scanned by someone else
         * nComments  := #Comments that have been recieved
         *
         * Everything has been put into one query for performance reasons.
         */
        $a = array();
        $query = "SELECT c1.access, c1.fullname, c1.nScans, c2.nComments
                    FROM (SELECT access, fullname, MAX(nScans) as nScans
                        FROM ((SELECT t2.access, t2.fullname, COUNT(DISTINCT a.account_id) as nScans
                            FROM tags t, tags t2, actions a, invitations i, invitations i2
                            WHERE a.type = 'O'
                            AND i.account_id = a.account_id
                            AND i.scan = '0'
                            AND t.type = 'P'
                            AND t.tag_id = i2.tag_id
                            AND i2.account_id = i.account_id
                            AND a.tag_id = t2.tag_id
                            GROUP BY t2.access, t2.fullname)
                        UNION
                        (SELECT t.access, t.fullname, 0
                            FROM tags t
                            WHERE t.type = 'O')) as t1
                        GROUP BY access, fullname) as c1, 
                        (SELECT access, fullname, MAX(nComments) as nComments
                        FROM ((SELECT t2.access, t2.fullname, COUNT(DISTINCT a.account_id) as nComments
                            FROM tags t, tags t2, actions a, invitations i, invitations i2
                            WHERE a.type = 'O'
                            AND i.account_id = a.account_id
                            AND i.scan = '0'
                            AND t.type = 'P'
                            AND t.tag_id = i2.tag_id
                            AND i2.account_id = i.account_id
                            AND a.tag_id = t2.tag_id
                            AND a.comment <> ''
                            GROUP BY t2.access, t2.fullname)
                        UNION
                        (SELECT t.access, t.fullname, 0
                            FROM tags t
                            WHERE t.type = 'O')) as t2
                        GROUP BY access, fullname) as c2
                    WHERE c1.fullname = c2.fullname
                    ORDER BY c1.nScans DESC, c2.nComments DESC
                    LIMIT 10";
        
        @mysqli_real_query($link,$query);
	    $result = mysqli_store_result($link);
	    $i=0;
	    $a = array();
	    if(mysqli_num_rows($result)>0)
	    {
		    $row=mysqli_fetch_assoc($result);
		    do{
		        if($row['access'] == "P"){
			        $a[$i]['nametitle']=$row['fullname'];
			    }
			    else{
			        $a[$i]['nametitle']="Private Poster";
			    }
			    $a[$i]['nScans']=$row['nScans'];
			    $a[$i]['nComments']=$row['nComments'];
			    $i++;
		    }while($row=mysqli_fetch_assoc($result));
	    }
        $json = addslashes(json_encode($a));

        $fileHandler = fopen("/var/www/vhosts/smart-qr.com/subdomains/grand/html/global/static/grand/generalOTable", 'w');
        fwrite($fileHandler, $json);
        fclose($fileHandler);
    }
    
    getPopularPeople();
    getPopularPosters();
    getLikedPosters();
    getControversialPosters();
    getGeneralPTable();
    getGeneralOTable();

?>
