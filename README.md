# wpstats
A simple PHP script for displaying the stats of your WordPress site using the WordPress stats.wordpress.com API.

![Example dashboard](https://github.com/foghorn/wpstats/blob/master/wpstats_example.PNG "Example dashboard")

# Overview
This script will use your API key from WordPress.com to grab the live statistics and display them in a simple format that's easy to slot into a dashboard. This will enable you to check the stats for multiple websites without needing to log into each one, and display them on a single page. It can also enable you to embed your stats into a dashboard with other information.

Note that the stats.wordpress.com API endpoint uses a simple authentication mechanism instead of the preferred OAuth method suggested in the documentation. Additionally, this is a legacy endpoint and is not currently maintained. It is not recommended to use this endpoint for critical operations as it may cease functioning at any time.

This script will work for your WordPress site assuming that you either have it hosted on WordPress.com or you have linked the site to your WordPress.com account through JetPack.

# Items Displayed
* Today: Current day's pageviews, colored based on a predictive model (see below)
* vs yesterday: Total pageviews for the previous calendar day
* vs last week: Total pageviews for the same day of the week in the previous week (7 days ago)
* 7d avg: A rolling average of the pageviews for the previous 7 days (not including the current day)
* 30d avg: A rolling average of the pageviews for the previous 30 days (not including the current day)
* Bar graph for visualizing the previous 14 days (including the current day)

# Quick Note About The Predictive Model
The color of the cell for the current day's post count will change based on that number's relation to the expected pageview count based on the time of day. The prediction is based on the proportion of traffic per hour seen on a WordPress website with over 2 million visitors per month based in the United States, with the percentage of traffic by hour expressed in an array near line 88. The number is re-computed every minute, assuming a consistent rate of increase between the two hours and using the 7 day average as the comparison baseline.

* If today's pageviews are above the current 7 day average for pageviews then the cell is GREEN
* If today's pageviews are above the predicted pagevies based on the time of day, the cell is BLUE
* If today's pageviews are within 10% of the predicted pageviews based on the time of day, the cell is YELLOW
* Otherwise the cell is RED

# Use Of These Files
Feel free to re-use any portion of these files for other projects. There do not seem to be many tutorials or snippets available for querying this data, so this was all generated through trial and error.

One word of caution: the CSV output for the API endpoint does not properly encapsulate all results, and therefore occasionally needs to be cleaned up prior to constructing an array to display the results.

# Included Files
## stats.php
This is the primary file and the one you will navigate to with your browser to display the stats. You will need to update at least two items within the file to get it to work properly, and there are additional options to allow you to tweak the results.

## black.png
A 10x10 black square to create the bar graph.

## white.png
A 10x10 white square to create the bar graph.

# Installation Guide
## 1: Download all files and update stats.php
You will need to edit two specific items:
* Your API key on line 26
* Your site's ID on line 29

Your API key is the one you were issued when you registered if you registered your account prior to 2011, or you may need to get one. [Please visit the WordPress site](https://wordpress.com/support/api-keys/) for more information on obtaining and API key.

Your site's ID can be obtained by logging into your WordPress site, navigating to the stats section of your Jetpack plugin, and clicking the link in the prompt to head to Wordpress.com for detailed stats. The ID will be in the URL for that link.

## 2: Optional tweaks
There are a couple items that are optional within the script.

For those who want to require a unique access token to be provided before the info will be displayed (for some minor additional security enhancement) there's a chunk of code at the top of the page you can un-comment.

Additionally, the code around line 32 will allow you to adjust for any differences between your server's timezone and your blog's timezone.

Finally, the code around line 50 will allow you to scale the bar graph displayed on the page to fit your traffic levels and page size.

## 3: Upload all to a server
This doesn't need to be a public server. If you're running an internal dashboard like LibreNMS you can spin up a small docker container with this script and a web server and it will work just fine. Otherwise, for public servers I recommend you use one with HTTPS configured.

## 4: Pick a visualization
There are three available visualizations that are accessed by using a different URL.

### stats.php
This is the standard visualization and will display a box with the current daily stats, and some information about performance over the previous 7 and 30 days. It will also compare the current day's stats to the same day the previous week, and attempt to determine if based on the current rate of viewers whether the views for that day will exceed the weekly average.

### stats.php?type=1
This visualization combines the summary chart and the bar chard discussed below.

### stats.php?type=2
This visualization provides a bar graph much like the one in the Jetpack stats page, but significantly smaller.