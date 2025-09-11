<?php

namespace NISU\Core;

class Image {

  /**
   * @var string Image size slug
   */
  private string $image_size;

  /**
   * @var int Image width
   */
  private int $width;

  /**
   * @var int Image height
   */
  private int $height;

  /**
   * @var string Relative filepath to the image file from the uploads folder
   */
  private string $relative_filepath;

  /**
   * @var string Absolute path to the uploads folder
   */
  private string $uploads_folder;

  /**
   * Constructor 
   * 
   * @param string $image_size Image size slug
   * @param array $metadata Image metadata
   * 
   * @return void
   */
  public function __construct(string $image_size, array $metadata) {
    $this->image_size = $image_size;

    $this->width = isset($metadata["width"]) ? $metadata["width"] : 0;
    $this->height = isset($metadata["height"]) ? $metadata["height"] : 0;

    $this->relative_filepath = isset($metadata["file"]) ? $metadata["file"] : "";

    $this->uploads_folder = $this->get_uploads_folder_base();
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Get absolute filepath to the image.
   * 
   * @return string Absolute filepath or empty string if uploads folder or relative path aren't defined
   */
  public function get_absolute_filepath(): string {

    if(!$this->uploads_folder || $this->absolute_filepath) {
      return "";
    }

    return $this->uploads_folder . "/" . $this->relative_filepath;
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Get default image size suffix used in the filenames.
   * 
   * @return string Default image size suffix or empty string if couldn't create one
   */
  public function get_default_image_size_suffix(): string {

    if(!$this->width || !$this->height) { // This should never be true but check just in case
      return "";
    }

    return $width . "x" . $heigth;
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Set new filepath for the image. Also updates the relative filepath if successful.
   */
  public function set_filepath(string $new_filepath): string {
    if(!$new_filepath) {
      return $this->relative_filepath;
    }


    /**
     * @todo CHECK THAT THE CODE BELOW WORKS. This has been written while on a plane 
     * and the code has not been tested at all.
     */

    $success = rename($this->get_absolute_filepath(), $new_filepath);
    if($success) {

      $this->relative_filepath = str_replace($this->uploads_folder, "", $new_filepath);
    }

    return $this->relative_filepath; 
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Get absolute path to the uploads folder.
   * 
   * @return string
   */
  private function get_uploads_folder_base(): string {

    // Get absolute path to the uploads folder base
    $uploads_location_data = wp_get_upload_dir(); // Can we trust that the result is guaranteed?

    return isset($uploads_location_data["basedir"]) ? $uploads_location_data["basedir"] : "";
  }
}
