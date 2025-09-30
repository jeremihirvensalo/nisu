<?php

namespace NISU\Core;

class Image {

  /**
   * @var string Absolute path to the uploads folder
   */
  private string $uploads_path = "";

  /**
   * @var string Relative path from the uploads folder base
   */
  private string $relative_path = "";

  /**
   * @var array Image size files
   */
  private array $image_sizes = [];

  /**
   * Constructor.
   * 
   * @param array $image_data Image data
   * 
   * @return void
   */
  public function __construct(array $image_data) {

    $this->set_uploads_path($image_data);
    $this->set_image_sizes($image_data);
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Get absolute path to the uploads folder.
   * 
   * @return string
   */
  public function get_uploads_path(): string {
    return $this->uploads_path;
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Get the image size files
   * 
   * @return array
   */
  public function get_image_sizes(): array {
    return $this->image_sizes;
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Set absolute path to the uploads folder.
   * 
   * @param array $image_data Image data. Must contain "file" key with the filename of the image.
   * 
   * @return string
   */
  private function set_uploads_path(array $image_data): void {
    
    if(!isset($image_data["file"])) {

      /**
       * @todo add logging?
       */
      return;
    }

    $path_parts = explode("/", $image_data["file"]);

    // Remove the filename
    array_pop($path_parts);

    // Get absolute path to the uploads folder base
    $uploads_location_data = wp_get_upload_dir(); // Can we trust that the result is guaranteed?
    $uploads_base = isset($uploads_location_data["basedir"]) ? $uploads_location_data["basedir"] : "";

    // Set the relative path from the uploads base
    $this->relative_path = implode("/", $path_parts);

    // Set the uploads path
    $this->uploads_path = $this->relative_path ? $uploads_base . "/" . $this->relative_path: $uploads_base;
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Set the image size files.
   * 
   * @param array $image_data Image data. Must contain "sizes" key with an array value.
   * 
   * @return array
   */
  private function set_image_sizes(array $image_data) {
    if(!isset($image_data["sizes"])) {

      /**
       * @todo add logging?
       */
      return;
    }

    $this->image_sizes = $image_data["sizes"];
  }
}
