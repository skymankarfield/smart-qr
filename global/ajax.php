<?php
    include("../_src/_php/_misc/decrypt.php");
    include("../_src/_php/_misc/encrypt.php");
    
    function get_node($id){
        $node = array();
        //DONT TOUCH THESE TWO
        if(strstr($id, "-like") !== false){
            $node['node']['name'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            $node['node']['id'] = $id;
            $node['node']['color'] = "#008800";
        }
        else if(strstr($id, "-dislike") !== false){
            $node['node']['name'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            $node['node']['id'] = $id;
            $node['node']['color'] = "#880000";
        }
        else if(strstr($id, "p") !== false){
            $id = substr($id, 1);
            $fileName = "../profiles/static/grand/private".$id;
            $fileHandler = fopen($fileName, 'r');
            $json = json_decode(stripslashes(fread($fileHandler, filesize($fileName))));
            
            if(strlen($json->fullName) > 20){
                $json->fullName = substr($json->fullName, 0, 20)."...";
            }
            if($json->public == "P"){
                $node['node']['name'] = $json->fullName;
            }
            else{
                $node['node']['name'] = "Annonymous";
            }
            //$node['node']['project'] = $json->project;
            $node['node']['id'] = "p".$id;
            fclose($fileHandler);
        }
        else if(strstr($id, "x") !== false){
            $id = substr($id, 1);
            $fileName = "../profiles/static/grand/private".$id;
            $fileHandler = fopen($fileName, 'r');
            $json = json_decode(stripslashes(fread($fileHandler, filesize($fileName))));
            
            if(strlen($json->fullName) > 20){
                $json->fullName = substr($json->fullName, 0, 20)."...";
            }
            if($json->public == "P"){
                $node['node']['name'] = $json->fullName;
            }
            else{
                $node['node']['name'] = "Annonymous";
            }
            //$node['node']['project'] = $json->project;
            $node['node']['id'] = "x".$id;
            fclose($fileHandler);
        }
        else if(strstr($id, "o") !== false){
            $id = substr($id, 1);
            $fileName = "../objects/static/grand/private".$id;
            $fileHandler = fopen($fileName, 'r');
            $json = json_decode(stripslashes(fread($fileHandler, filesize($fileName))));
            
            if(strlen($json->title) > 20){
                $json->title = substr($json->title, 0, 20)."...";
            }
            if($json->public == "P"){
                $node['node']['name'] = $json->title;
            }
            else{
                $node['node']['name'] = "Private Poster";
            }
            //$node['node']['project'] = $json->project;
            $node['node']['id'] = "o".$id;
            fclose($fileHandler);
        }
        else if(strstr($id, "l") !== false){
            $id = substr($id, 1);
            $fileName = "../objects/static/grand/private".$id;
            $fileHandler = fopen($fileName, 'r');
            $json = json_decode(stripslashes(fread($fileHandler, filesize($fileName))));
            
            if(strlen($json->title) > 20){
                $json->title = substr($json->title, 0, 20)."...";
            }
            if($json->public == "P"){
                $node['node']['name'] = $json->title;
            }
            else{
                $node['node']['name'] = "Private Poster";
            }
            //$node['node']['project'] = $json->project;
            $node['node']['id'] = "l".$id;
            fclose($fileHandler);
        }
        header('Content-type: application/json');
        return json_encode($node);
    }
    
    function get_connected_nodes($id){
        $edges = array();
        
        if(strstr($id, "p") !== false){
            $id = substr($id, 1);
            $fileName = "../myevent/myscans/static/grand/json".$id;
            $fileHandler = fopen($fileName, 'r');
            $json = json_decode(stripslashes(fread($fileHandler, filesize($fileName))));

            $i=0;
            foreach($json as $edge){
                $edges[$i]['edge']['a'] = "p".decrypt($edge->userID);
                $edges[$i]['edge']['b'] = "p".$id;
                $edges[$i]['edge']['weight'] = "2";
                $i++;
            }
            fclose($fileHandler);
        }
        else if(strstr($id, "o") !== false){
            $id = substr($id, 1);
            $fileName = "../objects/static/grand/comments".$id;
            $fileHandler = fopen($fileName, 'r');
            $json = json_decode(stripslashes(fread($fileHandler, filesize($fileName))));
            
            $i=0;
            foreach($json as $edge){
                $edges[$i]['edge']['a'] = "x".decrypt($edge->userID);
                $edges[$i]['edge']['b'] = "o".$id;
                $edges[$i]['edge']['weight'] = "2";
                $i++;
            }
            fclose($fileHandler);
        }
        else if(strstr($id, "l") !== false){
            $id = substr($id, 1);
            $fileName = "../objects/static/grand/comments".$id;
            $fileHandler = fopen($fileName, 'r');
            $json = json_decode(stripslashes(fread($fileHandler, filesize($fileName))));
            
            $i=0;
            foreach($json as $edge){
                $edges[$i]['edge']['a'] = "p".decrypt($edge->userID).str_replace("5", "-like", str_replace("1", "-dislike", $edge->rating));
                $edges[$i]['edge']['b'] = "o".$id;
                $edges[$i]['edge']['weight'] = "2";
                $i++;
            }
            fclose($fileHandler);
        }
        header('Content-type: application/json');
        return json_encode($edges);
    }
    
    switch($_GET['call']){
        case "get_node":
            echo get_node($_GET['id']);
            break;
        case "get_connected_nodes":
            echo get_connected_nodes($_GET['id']);
            break;
    }
?>
