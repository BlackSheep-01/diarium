<?php

require __DIR__ . "/inc/functions.inc.php";
require __DIR__ . "/inc/db-connect.inc.php";
date_default_timezone_set("Asia/Kolkata");

// user input form data from database extracted. user input -> database table -> card 
/* $_GET -> Php array which stores data sent via http get request (url parameters). 
    eg url: https://example.com/search.php?term=apple&sort=asc
    $_GET['term'] => "apple"
    $_GET['sort'] => "asc"
*/


$perPage= 4; //cards per page
// "page" key is generated and added to url via pagination links down below. Then $_GET can access it. 
$currentPage= (int) ($_GET["page"] ?? 1);  //reads current page number from the url -> "....example.com/?page=3". if "page" dont exist, defaults to 1
if($currentPage<1) $currentPage= 1;     //prevent pagination from going below 1, page 0 shouldnt exist
$offset= ($currentPage-1) * $perPage;  
/*  currentPage 1 → offset 0 -> skip to 0 → show rows 0, 1, 2, 3     as lIMIT= 4
    currentPage 2 → offset 4 -> skip to 4 → show rows 4, 5, 6, 7
Offset = skip this many rows before selecting.*/ 


$statementCount= $pdo -> prepare("SELECT COUNT(*) as `count` from `entries`");  //count number of rows/entries
$statementCount-> execute();
$count= $statementCount-> fetch(PDO::FETCH_ASSOC)["count"];
$numPagesNeeded= ceil($count/$perPage);


// SELECT * FROM table_name -> ORDER BY entry date,id -> LIMIT number_of_rows -> OFFSET number_of_rows_to_skip
// each table row corresponds to a card 
// each time we visit a new page of diary, the currentPage value will change -> $statement gets executed with new values automatically -> new cards are loaded automatically
$statement= $pdo -> prepare("SELECT * FROM `entries` ORDER BY `date` DESC, `id` DESC LIMIT $perPage OFFSET $offset");
$statement-> execute();
$results= $statement-> fetchAll(PDO::FETCH_ASSOC);
// var_dump($results);
?>



<?php require __DIR__ . "/views/header.views.php";  ?>


            <h1 class="main_heading">Entries</h1>


            <?php foreach($results as $result): ?>
                <div class="card">
                    <?php if( !empty($result["image"]) ): ?>
                        <div class="card_img_container">
                            <img class="card_img" src="user uploads/<?= $result["image"]?>" alt="">
                        </div>
                    <?php endif;?>

                    <div class="card_description_container">  
                        <?php 
                            $dateArray= explode("-",$result["date"]);        //eg: $result["date"]= "2025-07-23"
                            $timeStamp= mktime(12,0,0,$dateArray[1],$dateArray[2],$dateArray[0]);
                        ?>
                        <div class="card_time"> <?= e( date("m/d/Y", $timeStamp) ); ?> </div>         <!-- 07/23/2025 -->
                        <h2 class="card_heading"> <?= e($result["title"]); ?> </h2>    
                        <p class="card_paragraph"> <?= nl2br(e($result["message"])); ?> </p>  
                    </div>    
                </div>
            <?php endforeach; ?>
            

            <?php if($numPagesNeeded > 1): ?>   
                <ul class="pagination">
                    <?php if($currentPage > 1): ?>    
                        <li class="pagination_li">  <!--wont show on 1st page-->
                            <a class="pagination_link arrow" href="index.php?<?= http_build_query(["page"=>$currentPage-1]);?>">⏴</a>
                        </li>
                    <?php endif;?>

                    <!-- if pagination variable(i) = currentPage from page url, then that's the currently active page (i) -->
                    <!-- each pagination link dynamically generates its corresponding url. eg: when i=2 -> href=".../index.php?page=2" -->
                    <?php for($i=1; $i<=$numPagesNeeded; $i++): ?>
                        <li class="pagination_li">
                            <a class="pagination_link<?php if($i===$currentPage): ?> pagination_link_active<?php endif;?>" href="index.php?<?= http_build_query(["page"=>$i]); ?>" > 
                                <?= $i; ?>   
                            </a>      
                        </li>
                    <?php endfor ?>
                    
                    <?php if($currentPage < $numPagesNeeded): ?>
                        <li class="pagination_li">   <!--wont show on last page-->
                            <a class="pagination_link arrow" href="index.php?<?= http_build_query(["page"=>$currentPage+1]);?>">⏵</a> 
                        </li>
                    <?php endif;?>
                </ul>
            <?php endif?> 

        
<?php require __DIR__ . "/views/footer.views.php";   ?>

