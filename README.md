# NISU - Named Image Size URLs

Replaces the image size width and height with the size slug from the image filenames. 


## What does this plugin actually do?

By default when WordPress creates image sizes for a image the filenames are something like `lorem-ipsum-250x100.jpg`. That becomes a problem when using images in native JS blocks because if the image size definition changes so does the file URLs. Native blocks can't automatically update the links and this causes the links to break. 

This plugin solves this issue by renaming the size part from the files with the image size slug. So lets say that we have an image size named `jedi-business` with width `250` and height `100`. By default it would produce a image file with name `lorem-ipsum-250-100.jpg` and after *NISU* magic it would become `lorem-ipsum-jedi-business`. This way even if the width, height or cropping definitions change the links won't break. 


## Regenerating the image sizes

You can regenerate the image sizes trough the [wp media regenerate](https://developer.wordpress.org/cli/commands/media/regenerate/) command. If you want to get the default naming back for all images you can just deactivate this plugin and run the the mentioned command.


## Actions

### nisu_image_size_filename_changed

The `nisu_image_size_filename_changed` is ran after a image size filename has been changed. 

```php
add_action("nisu_image_size_filename_changed", function(string $new_filepath, string $old_filepath, string $image_size): void {

  if(file_exists($new_filepath)) {
    // Do stuff for example a redirect rule for the old filepath or something
  }

}, 10, 3);
```


## Filters


### nisu_skip_image_size

The `nisu_skip_image_size` provides a way to skip the file renaming for specific image sizes. 

```php
/**
 * @param bool $skip Skip renaming for the current image size
 * @param string $image_size The current image size
 * 
 * @return bool
 */
add_filter("nisu_skip_image_size", function(bool $skip, string $image_size): bool {

  // Skip size "jedi-business"
  if($image_size === "jedi-business") {
    return true;
  }

  return $skip;
}, 10, 2);

```
