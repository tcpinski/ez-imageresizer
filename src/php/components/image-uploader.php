<?php

include 'src/php/libraries/ImageResize.php';

error_reporting(E_ALL);

use \Gumlet\ImageResize;

class ImageUploader {

  private $max_file_size = 8000000; // 1Mb = 8e+6bytes

  function __construct() {

    $this->handleForm();

  }

  /**
   * Handles the form's input & resizes the images based on the uploaded image's size.
   */
  function handleForm() {

    if ( isset( $_GET['action_submit'] ) == 'action_submit' ) {

      $uploaded_files = $_FILES['file_upload'];


      foreach ($uploaded_files['tmp_name'] as $index=>$file_tmp_name) {
        $this->resizeImage($file_tmp_name, $uploaded_files['name'][$index], 'jpg');
      }
    }
  }

  /**
   * Resizes the given image based on its size.
   *
   * < 768px - LowRes, Small (SAME SIZE)
   * >= 768px AND <= 1200px - LowRes, Small, Medium (SAME SIZE)
   * > 1200px - LowRes, Small, Medium, Large(SAME SIZE) 
   * 
   * TODO: 
   */
  function resizeImage($file_tmp_name, $name, $type) {
    $image = new ImageResize($file_tmp_name);

    $name = explode('.', $name);
    $image_name = $name[0];
    $image_type = $name[1];

    // Small Image
    if ($image->getSourceWidth() < 768) {

      // Low Resolution
      $image->save("images/$image_name-small-lr.$image_type", null, 100);

      // Same Size
      $image->save("images/$image_name-small.$image_type", null, 100);
    }

    // Medium Image
    if ($image->getSourceWidth() >= 768 && $image->getSourceWidth() < 1200) {
      $image->save("images/$image_name-medium.$image_type");
    }

    // large Image
    if ($image->getSourceWidth() > 1200) {
      $image->save("images/$image_name-large.$image_type");
    }
  }

  function renderForm() { ?>
  
    <?php // action="?action_submit" ?>
      <form action="?action_submit" id="image-uploader" method="post" enctype="multipart/form-data">
        
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->max_file_size; ?>" />
        <input type="file" id="file_upload" name="file_upload[]" accept="image/*" multiple>
        <label class="mb-4" for="file_upload" title="Choose Images">Choose Images<br><i class="fas fa-file-upload mt-2"></i></label>
        <input type="submit" value="Resize" name="submit" title="Resize">

        <div class="row mt-5">

          <div class="image-uploader__list list--file-image col-6">
            <h3 class="mb-3">Selected Images</h3>
            <ul></ul>
          </div>

          <div class="image-uploader__list list--file-size col-6">
            <h3 class="mb-3">Size</h3>
            <ul></ul>
          </div>

        </div>

    </form>
  <?php }

}