<?php
/**
 * Created by PhpStorm.
 * User: aleric
 * Date: 14/01/2018
 * Time: 19:03
 */

namespace Inc\Classes;


use Inc\Base\BaseController;
use Inc\Database\DbCategory;

class Category extends BaseController {


    /**
     * @var array Collection of error messages
     */
    public $errors = array();
    /**
     * @var array Collection of success / neutral messages
     */
    public $messages = array();

    /**
     * Init function run form the Init class every
     * time that we load a page.
     */
    public function register() {
        if (isset($_POST['addCategory'])) {
            if ($id = $this->catchAdd()) {
                header("Location: $this->website_url/page.php?name=admin-area&category&edit&id=$id");
            }
            $this->showError();
        } else if (isset($_POST['updateCategory'])) {
            if ($id = $this->catchEdit()) {
                header("Location: $this->website_url/page.php?name=admin-area&category&edit&id=$id");
            }
            $this->showError();
        } else if (isset($_POST['deleteCategory'])) {
            if ($id = $this->catchDelete()) {
                header("Location: $this->website_url/page.php?name=admin-area&category");
            }
        }

    }


    /**
     * When clicked the button editAddress in edit-address.php
     * page the form send $_POST information and that function
     * permit to catch them.
     *
     * @return bool
     */
    public function catchAdd() {
        $title = $_POST['title'];
        $slug = empty($_POST['slug']) ?
            (str_replace(' ', '-', strtolower($title))) :
            (str_replace(' ', '-', strtolower($_POST['slug'])));
        $desc = $_POST['description'];
        $image = $_FILES["image"];

        if (!(empty($title))) {

            $data = array(
                "title" => $title,
                "description" => $desc,
                "slug" => $slug,
                "dateCreation" => DbCategory::now(),
                "dateLastUpdate" => DbCategory::now(),
            );

            // IMAGE
            if ($image['name']) {
                if ($idImage = Image::upload($image)) {
                    $data['idImage'] = $idImage;
                }
            }

            $existCategory = DbCategory::get(["slug" => $slug], "OBJECT"); // Get the address
            // if exist
            if (!$existCategory) {
                $messages[] = "Category inserted";
                return DbCategory::insert($data);

            } else {
                $this->errors[] = "The category already exist, please change the slug";
                return false;
            }
        } else {
            $this->errors[] = "Insert title";
            return false;
        }
    }

    /**
     * When clicked the button editAddress in edit-address.php
     * page the form send $_POST information and that function
     * permit to catch them.
     *
     * @return bool
     */
    public function catchEdit() {
        $id = $_GET['id'];
        $title = $_POST['title'];
        $slug = empty($_POST['slug']) ?
            (str_replace(' ', '-', strtolower($title))) :
            (str_replace(' ', '-', strtolower($_POST['slug'])));
        $desc = $_POST['description'];
        $image = $_FILES["image"];
        $valid = null;

        if (!(empty($title))) {

            $data = array(
                "title" => $title,
                "description" => $desc,
                "slug" => $slug,
                "dateLastUpdate" => DbCategory::now(),
            );

            // IMAGE
            if ($image['name']) {
                if ($idImage = Image::upload($image)) {
                    $data['idImage'] = $idImage;
                }
            } else {
                $data['idImage'] = null;
            }

            $categoryToEdit = DbCategory::get(["id" => $id], "OBJECT"); // Get the category from ID
            $categoryOfSlug = DbCategory::get(["slug" => $slug], "OBJECT"); // Get the category from SLUG

            // if exist
            if ($categoryToEdit) {
                // check the slug
                if (!$categoryOfSlug) {
                    // no one have this slug
                    $valid = true;
                } else {
                    // only if is the same slug we can update it
                    if (($categoryOfSlug->id === $categoryToEdit->id)) {
                        $valid = true;
                    } else {
                        $this->errors[] = "Slug not available";
                        $valid = false;
                    }
                }

                if ($valid) {
                    $messages[] = "Category inserted";
                    return DbCategory::update($data, ["id" => $id]) ? $id : false;
                } else {
                    return false;
                }
            }
        } else {
            $this->errors[] = "Insert title";
            return false;
        }
    }

    public function catchDelete() {
        $id = $_GET['id'];
        return DbCategory::delete(["id" => $id]);
    }

    /**
     * simply return the current state of the user's login
     *
     * @return boolean user's login status
     */
    public function showError() {
        if ($this->errors) { ?>
            <div class="admin-message message alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php
                foreach ($this->errors as $error) {
                    echo $error . " ";
                }
                ?>
            </div>
            <?php
        }
        if ($this->messages) { ?>
            <div class="admin-message message alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php
                foreach ($this->messages as $message) {
                    echo $message;
                }
                ?>
            </div>
            <?php
        }

    }

}