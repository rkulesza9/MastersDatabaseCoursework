<?php
function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE)
        {
           
            $data[] = $row;
        }
        fclose($handle);
    }
    return $data;
}
?>

<?php

	$load_csv_file = array();
	$load_csv_file["status"] = "";
	$load_csv_file["content"] = "";
	if(isset($_POST["load_csv_file"])){
		$filename = basename($_FILES["fileToUpload"]["name"]);

		if ($_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK               //checks for errors
		      && is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) { //checks that file is uploaded
		  	
		  	$load_csv_file["status"] = "success";
		  	$load_csv_file["content"] = "<strong>Success!</strong> $filename was successfully uploaded!";

			$file_content_arr = csv_to_array($_FILES['fileToUpload']['tmp_name']);

			if($file_content_arr == FALSE || substr($filename,-4) != ".csv"){
				$load_csv_file["csv-status"] = "failure";
				$load_csv_file["csv-content"] = "<strong>Error!</strong> $filename is not a valid csv file!";
			} else {
				$load_csv_file["csv-status"] = "success";
				$load_csv_file["csv-header"] = $file_content_arr[0];

				array_shift($file_content_arr);
				$load_csv_file["csv-data"] = $file_content_arr;
				$load_csv_file["rows"] = count($file_content_arr);
				$load_csv_file["cols"] = count($load_csv_file["csv-header"]);

				$one_day = 86400;
				setcookie("data-loaded","True", time() + $one_day , "/");

			}


		} else {
			$load_csv_file["status"] = "failure";
			$load_csv_file["content"] = "<strong>Error!</strong> $filename was not successfully uploaded!";
		}

		// if(substr($filename, -4) == ".csv"){
		// 	$load_csv_file["status"] = "success";
		// 	$load_csv_file["content"] = "<strong>Success!</strong> $filename was successfully uploaded!";
		// } else {
		// 	$load_csv_file["status"] = "failure";
		// 	$load_csv_file["content"] = "<strong>Error!</strong> $filename is not a csv file!";
		// }
	}

	echo json_encode($load_csv_file);
?>