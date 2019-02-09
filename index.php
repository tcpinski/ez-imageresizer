<?php

require('src/php/functions.php');

get_header();

?>

<div class="container text-center mt-5">
  <div class="row">
    <div class="col-12 col-md-8 offset-2">
      <form action="upload.php" class="image-uploader" method="post" enctype="multipart/form-data">
      
          <input type="file" id="file_upload" name="file_upload">
          <label class="mb-4" for="file_upload" title="Choose Images">Choose Images<br><i class="fas fa-file-upload mt-2"></i></label>

          <input type="submit" value="Resize" name="submit">
      </form>
    </div>
  </div>
</div>

<?php get_footer(); ?>