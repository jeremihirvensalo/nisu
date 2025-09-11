<?php

namespace NISU;

use NISU\Core\Image;

class Main {

  /**
   * @author Jeremi Hirvensalo
   * 
   * Changes the default image size filenames to include the size slug instead of the actual image size 
   * in pixels. The function is designed to be used with the `wp_generate_attachment_metadata` hook.
   * 
   * @param array $metadata Image metadata
   * 
   * @return array Image metadata
   */
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

      /**
       * Skip the renaming for the image size.
       * 
       * @param bool Skip renaming
       * @param string Image size slug
       * 
       * @since 0.1.0
       */
      if(apply_filters("nisu_skip_image_size", false, $size_slug)) {
        continue;
      }

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

  /**
   * @author Jeremi Hirvensalo
   * 
   * Get the image filepath with the image size slug.
   * 
   * @param string $absolute_filepath Absolute filepath to the image file
   * @param string $size_slug Image size slug
   * @param string $default_image_size_suffix The default image size filename suffix to replace
   * 
   * @return string The new absolute filepath
   */
  private static function get_new_image_size_filepath(string $absolute_filepath, string $size_slug, string $default_image_size_suffix): string {

    $default_size_slug_position = self::get_image_size_slug_position($default_image_size_suffix, $absolute_filepath);
    if($size_slug_position < 0) {
      return "";
    }

    return substr_replace($absolute_filepath, $size_slug, $size_slug_position, strlen($default_image_size_suffix));
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * @param string The default image size filename suffix
   * @param string Absolute filepath to the image
   * 
   * @return int Index of the last occurrence of the default size suffix or -1 if no match found
   */
  private static function get_image_size_slug_position(string $default_image_size_suffix, string $absolute_filepath): int {
    if(!$default_image_size_suffix) {
      return -1;
    }
    
    $match_position = strrpos($absolute_filepath, $default_image_size_suffix);
    return $match_position !== false ? $match_position : -1;
  }
}
