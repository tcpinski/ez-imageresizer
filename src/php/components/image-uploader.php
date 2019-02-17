<?php

include 'src/php/libraries/ImageResize.php';

error_reporting(E_ALL);

use \Gumlet\ImageResize;

class ImageUploader {

  private $max_file_size = 8000000; // 1Mb = 8e+6bytes
  private $output_directory = 'images/';
  private $zip_name = 'resized-images'; // Today's date is postfixed during zipImages();
  private $image_size_small = 768;
  private $image_size_medium = 992;
  private $image_size_large = 1200;

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

      $this->zipImages();
      $this->cleanOutputDirectory();

    }
  }

  /**
   * Zips all images in $this->$output_directory
   */
  function zipImages() {
    $zip = new ZipArchive();
    $zip->open($this->zip_name . '-' . date('d-m-Y') . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

    $files = glob($this->output_directory . "*");

    foreach($files as $file) {
      $zip->addFile($file);
    }

    $zip->close();
  }

  /**
   * Cleans the output directory by deleting all of the files inside.
   */
  function cleanOutputDirectory() {
    $files = glob($this->output_directory . '*');

    foreach($files as $file) {
      if (is_file($file)) {
        unlink($file);
      }
    }
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
    if ($image_width < $this->image_size_small) {

      // Low Resolution
      $this->saveImage($file_tmp_name, $image_name . '-low-res', $image_type, 10);

      // Same size
      $this->saveImage($file_tmp_name, $image_name . '-small', $image_type, 100);
    }

    // Medium Image
    if ($image_width >= $this->image_size_small && $image_width < $this->image_size_medium) {
    
      // Low Resolution
      $this->saveImage($file_tmp_name, $image_name . '-low-res', $image_type, 10);

      // Small
      $this->saveImage($file_tmp_name, $image_name . '-small', $image_type, 100, 'sm');

      // Same size
      $this->saveImage($file_tmp_name, $image_name . '-medium', $image_type, 100);
    }

    // large Image
    if ($image_width > $this->image_size_large) {

      // Low Resolution
      $this->saveImage($file_tmp_name, $image_name . '-low-res', $image_type, 10);

      // Small
      $this->saveImage($file_tmp_name, $image_name . '-small', $image_type, 100, 'sm');

      // Medium
      $this->saveImage($file_tmp_name, $image_name . '-medium', $image_type, 100, 'md');

      // Same size
      $this->saveImage($file_tmp_name, $image_name . '-large', $image_type, 100);
    }
  }

  /**
   * Saves an image to /images.
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
        $image = $this->setImageWidth($image, $file_tmp_name, $size);
      }

      imagepng($image, $destination, $quality);
      imagedestroy($image);
      
    } else if ($image_type == 'jpg') {

      $image = @imagecreatefromjpeg($file_tmp_name);

      // Resize the image
      if ($size) {
        $image = $this->setImageWidth($image, $file_tmp_name, $size);
      }

      imagejpeg($image, $destination, $quality);
      imagedestroy($image);
    }
  }

  /**
   * Resizes the image to the specified width while keeping the aspect ratio and returns it.
   */
  function setImageWidth($image, $file_tmp_name, $size) {

    $new_width = 0;

    switch ($size) {
      case 'sm':
        $new_width = $this->image_size_small;
        break;
      case 'md':
        $new_width = $this->image_size_medium;
        break;
    }
    
    list($image_width, $image_height) = getimagesize($file_tmp_name);

    $new_height = ($image_height / $image_width) * $new_width;
    $tmp_image = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($tmp_image, $image, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);

    return $tmp_image;
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