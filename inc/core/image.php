<?php

namespace NISU\Core;

class Image {

  private string $image_size;

  private int $width;
  private int $height;

  private string $relative_filepath;

  private string $uploads_folder;

  public function __construct(string $image_size, array $metadata) {
    $this->image_size = $image_size;

    $this->width = isset($metadata["width"]) ? $metadata["width"] : 0;
    $this->height = isset($metadata["height"]) ? $metadata["height"] : 0;

    $this->relative_filepath = isset($metadata["file"]) ? $metadata["file"] : "";

    $this->uploads_folder = $this->get_uploads_folder_base();
  }

  public function get_absolute_filepath(): string {

    if(!$this->uploads_folder || $this->absolute_filepath) {
      return "";
    }

    return $this->uploads_folder . "/" . $this->relative_filepath;
  }

  public function get_default_image_size_suffix(): string {

    if(!$this->width || !$this->height) { // This should never be true but check just in case
      return "";
    }

    return $width . "x" . $heigth;
  }

  private function get_uploads_folder_base(): string {

    // Get absolute path to the uploads folder base
    $uploads_location_data = wp_get_upload_dir(); // Can we trust that the result is guaranteed?

    return isset($uploads_location_data["basedir"]) ? $uploads_location_data["basedir"] : "";
  }
}
