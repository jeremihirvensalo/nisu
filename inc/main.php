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
   * @param array $image_data Image data
   * 
   * @return array Image data
   */
  public static function rename_image_sizes(array $image_data): array {

    $image = new \NISU\Core\Image($image_data);

    $image_sizes = $image->get_image_sizes();
    
    // Abort if the metadata is not for an image or something has gone wrong(?)
    if(empty($image_sizes)) {

      /**
       * @todo Some optional logging here?
       */
      return $image_data;
    }
    // Get absolute path to the image's folder
    $uploads_path = $image->get_uploads_path();

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

      $image_size = new \NISU\Core\Image_Size($size_slug, $metadata, $uploads_path);

      $absolute_filepath = $image_size->get_absolute_filepath();
      if(!file_exists($absolute_filepath)) {

        /**
         * @todo Some optional logging here?
         */
        continue;
      }

      // Get the default image size part of the url ("1920x1280" for example)
      $default_image_size_suffix = $image_size->get_default_image_size_suffix();

      // Get the filepath with the image size slug
      $new_filepath = self::get_new_image_size_filepath($absolute_filepath, $size_slug, $default_image_size_suffix);
      
      // $image->update_image_size_file($new_filename);
      $image_sizes[$size_slug]["file"] = $image_size->set_filepath($new_filepath);
    }

    $image_data["sizes"] = $image_sizes;
    return $image_data;
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

    $size_slug_position = self::get_image_size_slug_position($default_image_size_suffix, $absolute_filepath);
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
