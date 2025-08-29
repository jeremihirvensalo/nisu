<?php

namespace NISU;

class Image_Sizes {

  public function __construct() {

    add_filter("wp_generate_attachment_metadata", [$this, "modify_image_size_filenames"], 10, 1);
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Rename the image size files. Updates the attachment metadata and modifies the actual filenames 
   * of the image sizes.
   * 
   * @param array $metadata Attachment metadata
   * 
   * @return array Attachment metadata
   */
  public function modify_image_size_filenames(array $metadata): array {
    
    if(!isset($metadata["sizes"]) || empty($metadata["sizes"])) { // Abort if the metadata is not for an image or something has gone wrong(?)
      return $metadata;
    }
    
    // Get the absolute path to the uploads folder
    $target_path = $this->get_folder_path($metadata); 

    // Assign the sizes array reference to an variable to achieve cleaner code
    $image_sizes = &$metadata["sizes"];

    foreach($image_sizes as $size_name => $image_data) {

      // Get the absolute path to the image size file
      $image_relative_path = isset($image_data["file"]) ? $image_data["file"] : "";
      $image_path = "$target_path/$image_relative_path";

      if(!file_exists($image_path)) { // I don't know if this case is possible but just in case check for it
        return $metadata;
      }
    
      // Get the default WordPress image size file suffix
      $image_size_suffix = $image_data["width"] . "x" . $image_data["height"];
      $match_position = strrpos($image_relative_path, $image_size_suffix);
      if($match_position !== false) {

        $new_filename = substr_replace($image_relative_path, $size_name, $match_position, strlen($image_size_suffix));

        $success = rename($image_path, "$target_path/$new_filename");
        if($success) {
          $image_sizes[$size_name]["file"] = $new_filename;
        }
      }
    }

    return $metadata;
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Get absolute path to the image folder.
   * 
   * @param array $metadata Image metadata to extract the target folder from
   * 
   * @return string Absolute path to the image folder.
   */
  private function get_folder_path(array $metadata): string {

    // Get absolute path to the uploads folder base
    $uploads_location_data = wp_get_upload_dir(); // Can we trust that the result is guaranteed?´
    $basedir = isset($uploads_location_data["basedir"]) ? $uploads_location_data["basedir"] : "";

    if(!$basedir) { // Just in case make sure that the upload dir base was found 
      return "";
    }

    $path_parts = explode("/", $metadata["file"]);
    array_pop($path_parts);
  
    return "$basedir/" . implode("/", $path_parts);
  }
}
