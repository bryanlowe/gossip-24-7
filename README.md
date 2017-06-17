Website Editing Guide

##################################
Site Folders:
Production - /home/gossip24/public_html/apps/production
Staging - /home/gossip24/public_html/apps/staging
Admin - /home/gossip24/public_html/apps/admin

##################################
How to edit the HTML:
Location: /home/gossip24/public_html/apps/[production or staging or admin]/views

Explanation: Each of the folders within view represents a page on the site. The files within these folders contain the HTML for that page. The files that are actually used for the site have a “.twig” extension because they are rendered using PHP Twig. This allows PHP and HTML to be integrated together seamlessly. “index.twig” is the main HTML page for each page. All other files are used as includes when needed by “index.twig”(this includes menus, sidebars, and other types of lists). Page specific JavaScript files are normally registered at the top of “index.twig”.

##################################
How to edit the CSS:
Location: /home/gossip24/public_html/apps/[production or staging or admin]/web/css

Explanation: You can edit or add new CSS files in this location. The site theme CSS file is called “style.css” on the production and staging sites. The custom styles override file is called “custom-site.css”

##################################
How to edit the JS:
Location: /home/gossip24/public_html/apps/[production or staging or admin]/web/js

Explanation: Global JavaScript files are located in this folder. Page specific JavaScript files are stored in the “page-scripts” folder and are generally named after the page that they control(this is only relevant to the admin site).

##################################
How to modify global CSS and JS includes:
Location: /home/gossip24/public_html/apps/[production or staging or admin]/assets/AppAsset.php

Explanation: This file contains all the CSS and JS file includes that would normally be set in the HTML. The CSS will be stored in an array called $css and the JavaScript will be stored in an array called $js. When loaded into the HTML, the CSS is loaded at the top and the JavaScript is loaded at the bottom of the page. If you are loading page specific scripts through the view, please keep in mind that this end up being loaded before the global scripts so if your page script depends on a global script, you may want to include the global script in the view instead.

##################################
How to edit the PHP:
Locations:
•	Controllers: /home/gossip24/public_html/apps/[production or staging or admin]/controllers
•	Models: /home/gossip24/public_html/apps/[production or staging or admin]/models

Explanation: Controllers generate the data needed to render the page and format the data retrieved from the page. Controllers include models to handle database data. Models validate the data received from the page and provide the functionality to pull data from the database. 

##################################
Story Uploads:
Location: /home/gossip24/public_html/uploads/story/[story id]/[images or audio]

Explanation: These locations are where files are uploaded for each story, whether they are images or audio(Video is not currently stored on the server). The admin tool saves files to these locations when you upload an image or an audio file. The production and staging sites read from these locations.

##################################
Repository Information
Website: https://bitbucket.org
Repo URL: https://BlacKMenac3@bitbucket.org/BlacKMenac3/gossip-24-7.git

Explanation: This uses GIT for subversion, so you may want to use source tree like we do at work in order to maintain these files.

##################################
References:
Yii2 Framework: http://www.yiiframework.com/doc-2.0/guide-index.html
PHP Guide: http://php.net/manual/en/intro-whatis.php
Twig PHP Guide: https://twig.sensiolabs.org/

Contact Author:
Bryan Lowe
email: mr.bryan.lowe@gmail.com