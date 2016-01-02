# Vertical Timeline for Drupal 8

The inspiration for this timeline is at: http://www.websterhall.com/timeline/. The final solution borrows ideas from lots of sources
to create a responsive, infinitely scrolling, vertical timeline that takes up two columns on large screens and collapses down to a
single column on small screens. It uses MasonryJS rather than a standard grid layout to keep the source order right but fill in the
gaps in the two column timeline when the content is irregularly sized. Most effects are pure css.

## Requirements:
- MasonryJS (http://masonry.desandro.com/)
  Download from http://masonry.desandro.com/ and place in /libraries/masonry.
## Suggested:
- Views Infinite Scroll (https://www.drupal.org/project/views_infinite_scroll).
  Ignore the instructions about downloading the autopager library, that is no longer needed.

## To try this out:

- Download MasonryJS from http://masonry.desandro.com/ and place in /libraries/masonry.
- Download Views Infinite Scroll from https://www.drupal.org/project/views_infinite_scroll and enable it.
- Download and enable this module.
- You will see an example timeline in Views called Test Timeline that provides a vertical timeline view of articles. You can adjust it to your needs, or use it as an example.

## Credits:

The original inspiration:
http://www.websterhall.com/timeline/

A starting point for a basic, responsive, two column vertical timeline:
http://codepen.io/codyhouse/full/FdkEf

Adding MasonryJS to fill in irregularly sized items in the two column layout:
http://masonry.desandro.com/

Adding dynamic classes that correspond to their current masonry position:
http://www.9lessons.info/2012/01/facebook-timeline-design-using-jquery.html

Borrowing ideas from D7 for custom queries, paging and infinite scrolling:
http://thinkshout.com/blog/2014/06/creating-an-infinite-scroll-masonry-block-without-views/

Creating CSS-only callouts and circles:
https://css-tricks.com/snippets/css/css-triangle/
