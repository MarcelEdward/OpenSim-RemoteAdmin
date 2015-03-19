<?php

require_once('RemoteAdmin.class.php');

$remoteAdmin = new RemoteAdmin();
$remoteData = $remoteAdmin->get_data();

$task = $_REQUEST['task'];

switch ($task)
{
    default:
        echo "<h2>Choose the method for the Remote Admin:</h2><table border =\"1\" cellpadding=\"5\">";
        $group = "";
        foreach ($remoteData AS $key=>$data)
        {
            if ($data->group != $group) {
                $group = $data->group;
                echo "<th colspan=\"2\"><h3>".$group."</h3></th>";
            }
            echo "<tr><td><a href=\"index.php?task=form&method=".$key."\">".$key."</a></td><td>".$data->description."</td></tr>";
        }
        echo "</table>";
        break;
    case 'form':
        $method = $_REQUEST['method'];
        $formData = $remoteData->$method;
        echo "<h2>Remote Admin: ".$method."<h2>";
        echo $formData->description."<br><br>";
        echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">";
        echo "<input type=\"hidden\" name=\"method\" value=\"".$method."\">";
        echo "<input type=\"hidden\" name=\"task\" value=\"post\">";
        if (is_object($formData->Required))
        {
            echo "<table border =\"0\" cellpadding=\"5\"><th colspan=\"3\">Required fields: </th>";
            foreach ($formData->Required AS $key => $field)
            {
                $default = "";
                if (isset($field->default)) $default = " value=\"".$field->default."\"";
                switch ($field->type)
                {
                    default:
                        echo "<tr><td>".$key."</td><td><input type=\"text\" name=\"dat[".$key."]\"$default></td><td>".$field->description."</td></tr>";
                        break;
                    case 'hidden':
                        echo "<input type=\"hidden\" name=\"dat[".$key."]\"$default readonly>";
                        break;
                    case 'readonly':
                        echo "<tr><td>".$key."</td><td><input type=\"text\" name=\"dat[".$key."]\"$default readonly></td><td>".$field->description."</td></tr>";
                        break;                        
                    case 'file':
                        echo "<tr><td>".$key."</td><td><input type=\"file\" name=\"dat[".$key."]\"$default></td><td>".$field->description."</td></tr>";
                        break;
                    case 'boolean':
                        echo "<tr><td>".$key."</td><td><input type=\"checkbox\" name=\"dat[".$key."]\" value=\"True\" checked=\"checked\"$default></td><td>".$field->description."</td></tr>";
                        break;
                }
            }
            echo "</table>";
        }
        if (is_object($formData->Optional))
        {
            echo "<table border =\"0\" cellpadding=\"5\"><th colspan=\"3\">Optional fields: </th>";
            foreach ($formData->Optional AS $key => $field)
            {
                $default = "";
                if (isset($field->default)) $default = " value=\"".$field->default."\"";                
                switch ($field->type)
                {
                    default:
                        echo "<tr><td>".$key."</td><td><input type=\"text\" name=\"dat[".$key."]\"$default></td><td>".$field->description."</td></tr>";
                        break;
                    case 'hidden':
                        echo "<input type=\"hidden\" name=\"dat[".$key."]\"$default readonly>";
                        break;
                    case 'readonly':
                        echo "<tr><td>".$key."</td><td><input type=\"text\" name=\"dat[".$key."]\"$default readonly></td><td>".$field->description."</td></tr>";
                        break;                    
                    case 'file':
                        echo "<tr><td>".$key."</td><td><input type=\"file\" name=\"dat[".$key."]\"$default></td><td>".$field->description."</td></tr>";
                        break;
                    case 'boolean':
                        echo "<tr><td>".$key."</td><td><input type=\"checkbox\" name=\"dat[".$key."]\" value=\"True\" checked=\"checked\"$default></td><td>".$field->description."</td></tr>";
                        break;
                }
            }
            echo "</table>";
        }
        echo "<br><input type=\"submit\" value=\"Submit\"></form>";
        echo "<br><h3>Returned parameters:<h3> ".$formData->ReturnedParameters;
        if (strlen($formData->ErrorMessages)>0) echo "<br><br><h3>Error messages: </h3>".$formData->ErrorMessages;
        if (strlen($formData->Notes)>0) echo "<br><br><h3>Error messages: </h3>".$formData->Notes;
        
        break;
    case 'post':
        $method = $_POST['method'];
        $parameters = $_POST['dat'];
        if (count($_FILES['dat']['name'])>0) {
            require_once('config.php');
            $parameters['filename'] = $file_dest_dir.$_FILES['dat']['name']['filename'];
            move_uploaded_file($_FILES['dat']['tmp_name']['filename'],$parameters['filename']);
        }
        echo "<h2>Remote Admin: ".$method."</h2>";
        echo "<h3>Results of the call to the simulator.</h3>";
        $result = $remoteAdmin->SendCommand($method, $parameters);
        
        echo "<table border =\"1\" cellpadding=\"5\"><th><b>Parameter</th><th>Value</th>";
        foreach ($result AS $key=>$res)
        {
            if ($key=='lastlogin') {
                $res = date('Y-m-d H:m:i',$res);
            }
            echo "<tr><td>".$key."</td><td>".$res."</td></tr>";
        }
        echo "</table>";
        
        echo "<h3>Espected Results:</h3>";
        echo $remoteData->$method->ReturnedParameters;
        break;
}
?>
