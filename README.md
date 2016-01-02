# Vertical Timeline for Drupal 8

The inspiration for this timeline is at: http://www.websterhall.com/timeline/. The final solution borrows ideas from lots of sources
to create a responsive, infinitely scrolling, vertical timeline that takes up two columns on large screens and collapses down to a
single column on small screens. It uses MasonryJS rather than a standard grid layout to keep the source order right but fill in the
gaps in the two column timeline when the content is irregularly sized. Most effects are pure css.

Requirements:
- MasonryJS
  Download from http://masonry.desandro.com/ and place in /libraries/masonry


FAQ




Credits:

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
