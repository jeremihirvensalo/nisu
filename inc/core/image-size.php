<?php

namespace NISU\Core;

class Image_Size {

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
   * @var string Filename of the image size file
   */
  private string $filename;

  /**
   * @var string Absolute path to the uploads folder
   */
  private string $uploads_folder;

  /**
   * Constructor 
   * 
   * @param string $image_size Image size slug
   * @param array $metadata Image metadata
   * @param string $uploads_path Absolute path to the correct folder inside uploads
   * 
   * @return void
   */
  public function __construct(string $image_size, array $metadata, string $uploads_path) {
    $this->image_size = $image_size;

    $this->width = isset($metadata["width"]) ? $metadata["width"] : 0;
    $this->height = isset($metadata["height"]) ? $metadata["height"] : 0;

    $this->filename = isset($metadata["file"]) ? $metadata["file"] : "";

    $this->uploads_folder = $uploads_path;
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Get absolute filepath to the image.
   * 
   * @return string Absolute filepath or empty string if uploads folder or relative path aren't defined
   */
  public function get_absolute_filepath(): string {

    if(!$this->uploads_folder || !$this->filename) {
      return "";
    }

    return $this->uploads_folder . "/" . $this->filename;
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

    return $this->width . "x" . $this->height;
  }

  /**
   * @author Jeremi Hirvensalo
   * 
   * Set new filepath for the image. Also updates the relative filepath if successful.
   */
  public function set_filepath(string $new_filepath): string {
    if(!$new_filepath) {
      return $this->filename;
    }


    /**
     * @todo CHECK THAT THE CODE BELOW WORKS. This has been written while on a plane 
     * and the code has not been tested at all.
     */

    $success = rename($this->get_absolute_filepath(), $new_filepath);
    if($success) {

      $this->filename = str_replace($this->uploads_folder . "/", "", $new_filepath);
    }

    return $this->filename; 
  }
}
