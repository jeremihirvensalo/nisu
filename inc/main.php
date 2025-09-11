<?php

namespace NISU;

use NISU\Core\Image;

class Main {

  public static function rename_image_sizes(array $metadata): array {
    
    // Abort if the metadata is not for an image or something has gone wrong(?)
    if(!isset($metadata["sizes"]) || empty($metadata["sizes"])) {

      /**
       * @todo Some optional logging here?
       */
      return $metadata;
    }
    
    $image_sizes = &$metadata["sizes"];
    foreach($image_sizes as $size_slug => $metadata) {

      $image = new NISU\Core\Image($size_slug, $metadata);

      $absolute_filepath = $image->get_absolute_filepath();
      if(!file_exists($absolute_filepath)) {

        /**
         * @todo Some optional logging here?
         */
        continue;
      }

      $default_image_size_suffix = $image->get_default_image_size_suffix();

      // Get the filepath with the image size slug
      $new_filepath = self::get_new_image_size_filepath($absolute_filepath, $size_slug, $default_image_size_suffix);
      
      // Set the new filepath and save the location information
      $image_sizes[$size_slug]["file"] = $image->set_filepath($new_filepath);
    }

    return $metadata;
  }

  private static function get_new_image_size_filepath(string $absolute_filepath, string $size_slug, int $default_image_size_suffix): string {

    $default_size_slug_position = self::get_image_size_slug_position($default_image_size_suffix, $absolute_filepath);
    if($size_slug_position < 0) {
      return "";
    }

    return substr_replace($absolute_filepath, $size_slug, $size_slug_position, strlen($default_image_size_suffix));
  }

  private static function get_image_size_slug_position(string $default_image_size_suffix, string $absolute_filepath): int {
    if(!$default_image_size_suffix) {
      return -1;
    }
    
    $match_position = strrpos($absolute_filepath, $default_image_size_suffix);
    return $match_position !== false ? $match_position : -1;
  }
}
