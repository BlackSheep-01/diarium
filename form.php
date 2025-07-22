<?php
require __DIR__ . "/inc/functions.inc.php";
require __DIR__ . "/inc/db-connect.inc.php";

/* $_POST ->  Php array which stores data sent via http post request. For forms: Only form elements with a "name" attribute get added to the $_POST array.
 if "name" exists, the form's "name" and its corresponding (predefined/user-input) "value" becomes key:value pairs in $_POST array. 
*/

//user input from form inserted into database table
// var_dump($_POST);
if( !empty($_POST) ){
    $date= (string) ($_POST["date"] ?? "");  // ?? checks if value set & not null
    $title= (string) ($_POST["title"] ?? "");
    $message= (string) ($_POST["message"] ?? "");
    $finalImageName= null;  //before checking if any img is uploaded


    /* Uploaded image is taken, image name is modified to prevent anomalies, image is refactored into a common dimension */
    //Php is able to figure out everyting about a file from the file location itself.
    if( !empty($_FILES) and !empty($_FILES["image"]) ){    //$_FILES: php array that stores uploaded files
        if( $_FILES["image"]["error"] ===0 && $_FILES["image"]["size"] !== 0){
            $mime = mime_content_type($_FILES["image"]["tmp_name"]);  //verify the real type of uploaded image
            if ($mime !== "image/jpeg") {
                die("Only JPG or JPEG images are allowed.");
            }
            
            $nameWithoutExtension= pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME);
            $name= preg_replace("/[^a-zA-Z0-9]/","",$nameWithoutExtension);   //remove special charcaters from file name

            $originalImageLocation= $_FILES["image"]["tmp_name"];    //"tmp_name" is the location on server where the image is stored temporarily before its processed
            $finalImageName= $name . ".jpg";
            $finalImageLocation= __DIR__ . "/user uploads/" . $finalImageName;   //location where the final version of the image will get stored
            [$width, $height]= getimagesize($originalImageLocation);

            $maxDimension= 400;
            $scaleFactor= $maxDimension / max($width,$height);
            $newWidth= $width*$scaleFactor;
            $newHeight= $height*$scaleFactor;

            $img= imagecreatefromjpeg($originalImageLocation);   //loads uploaded original image into memory
            $newImg= imagecreatetruecolor($newWidth,$newHeight);   //create a blank canvas with the new dimensions
            imagecopyresampled($newImg,$img,0,0,0,0,$newWidth,$newHeight,$width,$height);  //copy the original image into blank canvas and resizes it

            imagejpeg($newImg,$finalImageLocation);   //stores the final version of the image in its destination
        }
    }


    $statement= $pdo-> prepare("INSERT INTO `entries` (`date`,`title`,`message`,`image`) VALUES (:datee,:titlee,:messagee,:imagee)");
    $statement-> bindValue(":datee",$date);
    $statement-> bindValue(":titlee",$title);
    $statement-> bindValue(":messagee",$message);
    $statement-> bindValue(":imagee",$finalImageName);
    $statement-> execute();

    echo "<a href='index.php'>Go back to Diary</a>";
    die();
}
?>



<?php require __DIR__ . "/views/header.views.php"; ?>


            <h1 class="main_heading">New Entry</h1>

            <!-- form "post" method sends form data in body of the http request(safer,invinsible) opposed to "get" which sends data via the url(unsafe,visible) -->
            <form action="form.php" method="post" enctype="multipart/form-data">    <!-- enctype for file upload -->
                <div class="form_group" id="form-date">
                    <label for="date" class="form_group_label">Date :</label>
                    <input type="date" id="date" name="date" class="form_group_input" required>
                </div>
                <div class="form_group">
                    <label for="title" class="form_group_label">Title :</label>
                    <input type="text" id="title" name="title" class="form_group_input" required>
                </div>
                <div class="form_group" id="form-msg">
                    <label for="message" class="form_group_label">Message :</label>
                    <textarea name="message" id="message" class="form_group_input" rows="6" required></textarea>
                </div>
                <div class="form_group">
                    <label for="image" class="form_group_label">JPG Image :</label>
                    <input type="file" id="image" name="image" class="form_group_input">
                </div>
                <div class="form_submit">
                    <button class="button">
                        <svg viewBox="0 0 34.7163912799 33.4350009649" class="button_plus">
                            <g style="fill: none;
                                    stroke: currentColor;
                                    stroke-linecap: round;
                                    stroke-linejoin: round;
                                    stroke-width: 2px;">
                            <polygon class="uuid-227ecc73-5fef-4efb-920c-3f9dd27ef3fc" points="20.6844359446 32.4350009649 33.7163912799 1 1 10.3610302393 15.1899978903 17.5208901631 20.6844359446 32.4350009649"/>
                            <line class="uuid-227ecc73-5fef-4efb-920c-3f9dd27ef3fc" x1="33.7163912799" y1="1" x2="15.1899978903" y2="17.5208901631"/>
                            </g>
                        </svg>
                        Save!
                    </button>
                </div>
                
            </form>


<?php require __DIR__ . "/views/footer.views.php"; ?>

       