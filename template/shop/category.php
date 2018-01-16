<?php

use \Inc\Database\DbCategory;
use \Inc\Database\DbProduct;
use \Inc\Database\DbImage;

$currentCategory = null;
if (!isset($_GET["category"]) && empty($_GET["category"])) {
    header("Location: page.php?name=home");
}

// current category
$currentCategory = DbCategory::get(["slug" => $_GET["category"]], 'object');
$imageCat = DbImage::get(["id" => $currentCategory->idImage], 'object');

// current product
$products = DbProduct::get(["idCategory" => $currentCategory->id], 'object');

//all categories
$categories = DbCategory::getAll('object');


require_once($baseController->website_path . "/template/_header.php");


?>
    <main role="main" class="page container">

        <div class="row row-offcanvas row-offcanvas-right">

            <div class="col-12 col-md-9">
                <p class="float-right d-md-none">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="offcanvas">Toggle nav</button>
                </p>

                <div class="jumbotron j-shop"
                     style="background-image:
                             linear-gradient(to bottom, rgba(0,0,0,.20), rgba(0,0,0,.30)),
                             linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.05) 25%, rgba(0,0,0,0.5) 75%, rgba(0,0,0,0.8) 100%),
                             url('<?php echo $baseController->website_url . $imageCat->path ?>');">
                    <h1><?php echo $currentCategory->title; ?></h1>
                    <p><?php echo $currentCategory->description; ?>
                    </p>
                </div>

                <div class="container-fluid">
                    <!-- add extra container element for Masonry -->
                    <div class="grid row">
                        <div class="grid-sizer col-xs-6 col-sm-4 col-md-4">

                        </div>
                        <?php
                        $i = 0;
                        foreach ($products as $product) {
                            $image = DbImage::get(["id" => $product->idImage], 'object');
                            $imagePath = $image ? $image->path : "/assets/img/no-image.jpg";
                            ?>
                            <div class="grid-item col-xs-6 col-sm-4 col-md-4">
                                <div class="card">
                                    <img class="card-img-top"
                                         src="<?php echo $baseController->website_url . $imagePath ?>"
                                         alt="Card image cap">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $product->title ?></h5>
                                        <p class="card-text"><?php echo $product->description ?></p>
                                    </div>
                                    <div class="card-footer">
                                        <span>€<?php echo $product->price ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div><!--/span-->

            <div class="col-6 col-md-3 sidebar-offcanvas" id="sidebar">
                <div class="list-group">
                    <a href="<?php echo $baseController->website_url ?>/page.php?name=shop"
                       class="list-group-item">Shop</a>
                    <?php
                    $i = 0;
                    foreach ($categories as $category) { ?>
                        <a href="<?php echo $baseController->website_url . "/page.php?name=category&category=" . $category->slug; ?>"
                           class=" list-group-item <?php echo $category->slug == $currentCategory->slug ? "active" : "" ?>">
                            <?php echo $category->title; ?>
                        </a>
                    <?php } ?>
                </div>
            </div><!--/span-->
        </div><!--/row-->
    </main>

<?php

require_once($baseController->website_path . "/template/_footer.php");

