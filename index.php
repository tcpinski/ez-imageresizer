<?php

require('src/php/functions.php');
require('src/php/components/image-uploader.php');

$image_uploader = new ImageUploader();

get_header();

?>

<div class="container text-center mt-5">
  <div class="row">
    <div class="col-8 offset-2">
      <?php $image_uploader->renderForm(); ?>
    </div>
  </div>
</div>

<?php get_footer(); ?>