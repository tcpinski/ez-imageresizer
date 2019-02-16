<?php

include 'src/php/libraries/ImageResize.php';

error_reporting(E_ALL);

use \Gumlet\ImageResize;

class ImageUploader {

  private $max_file_size = 8000000; // 1Mb = 8e+6bytes
  private $output_directory = 'images/';
  private $image_size_small = 768;
  private $image_size_medium = 992;

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
        $this->resizeImage($file_tmp_name, $uploaded_files['name'][$index]);


      }

      // $this->zipImages();
      // $this->cleanOutputDirectory();

    }
  }

  /**
   * Zips all images in $this->$output_directory
   */
  function zipImages() {
    $zip = new ZipArchive();
    $compress = $zip->open("images/images.zip", ZIPARCHIVE::CREATE);

    if ($compress === true) {
      $zip->addFile($image);
    }

    $zip->close();
  }

  /**
   * Cleans the output directory by deleting all of the files inside.
   */
  function cleanOutputDirectory() {

  }

  /**
   * Resizes the given image based on its size.
   *
   * lr, sm, md, lg
   * 
   * RESIZE DIMENSIONS
   * sm: 768px;
   * md: 992px;
   * 
   * < 768px - LowRes, Small (SAME SIZE)
   * >= 768px AND <= 1200px - LowRes, Small, Medium (SAME SIZE)
   * > 1200px - LowRes, Small, Medium, Large(SAME SIZE)
   */
  function resizeImage($file_tmp_name, $name) {

    $name = explode('.', $name);
    $image_name = $name[0];
    $image_type = $name[1];
    $image_width = getimagesize($file_tmp_name)[0];

    // Small Image
    if ($image_width < 768) {

      // Low Resolution
      $this->saveImage($file_tmp_name, $image_name . '-lr', $image_type, 10);

      // Same size
      $this->saveImage($file_tmp_name, $image_name . '-sm', $image_type, 100);
    }

    // Medium Image
    if ($image_width >= 768 && $image_width < 1200) {
    
      // Low Resolution
      $this->saveImage($file_tmp_name, $image_name . '-lr', $image_type, 10);

      // Small
      $this->saveImage($file_tmp_name, $image_name . '-sm', $image_type, 100, 'sm');

      // Same size
      $this->saveImage($file_tmp_name, $image_name . '-md', $image_type, 100);
    }

    // large Image
    if ($image_width > 1200) {

      // Low Resolution
      $this->saveImage($file_tmp_name, $image_name . '-lr', $image_type, 10);

      // Small
      $this->saveImage($file_tmp_name, $image_name . '-sm', $image_type, 100, 'sm');

      // Medium
      $this->saveImage($file_tmp_name, $image_name . '-md', $image_type, 100, 'md');

      // Same size
      $this->saveImage($file_tmp_name, $image_name . '-lg', $image_type, 100);
    }
  }

  /**
   * Saves an image to /images.
   * 
   * TODO: Resize the images.
   */
  function saveImage($file_tmp_name, $image_name, $image_type, $quality, $size = null) {

    $destination = $this->output_directory . "/$image_name.$image_type";

    if ($image_type == 'png') {

      $image = imagecreatefrompng($file_tmp_name);

      // PNG quality must be 0 (Highest) to 9 (Lowest)
      // Turn scale 0 - 100 into 0 - 9.
      $quality =  10 - ( ceil($quality) / 10 );

      // Resize the image
      if ($size) {

      }

      imagepng($image, $destination, $quality);
      imagedestroy($image);
      
    } else if ($image_type == 'jpg') {

      $image = @imagecreatefromjpeg($file_tmp_name);

      // Resize the image
      if ($size) {

      }

      imagejpeg($image, $destination, $quality);
      imagedestroy($image);
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