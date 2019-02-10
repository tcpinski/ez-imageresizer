<?php

require('src/php/functions.php');

get_header();

?>

<div class="container text-center mt-5">
  <div class="row">
    <div class="col-8 offset-2">
      <form action="upload.php" id="image-uploader" method="post" enctype="multipart/form-data">
      
          <input type="file" id="file_upload" name="file_upload" accept="image/*" multiple>
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
    </div>
  </div>
</div>

<?php get_footer(); ?>