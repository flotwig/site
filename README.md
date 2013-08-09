flotwig/site
=====

The source code of my personal site at za.chary.us

Goals
-----

 * Be concise
 
 * Be efficient
 
 * Be secure
 
 * Be functional
 
To achieve these ends, this project will:

 * Query the filesystem as little as possible
 
 * Avoid extraneous output
 
 * Enforce reasonable client-side caching whenever possible
 
 * Take minimal user input, and never directly use user input
 
Filesystem Structure
-----

 * `/README.md` This file
 
 * `/vhost.conf` The nginx config include to set up caching, rewrites, etc.
 
 * `/index.php` Code for the controller
 
 * `/content/` Main content directory
 
 * `/modules/` PHP files to handle requests to the controller
 
 * `/libs/` Third party libraries
 
Coding Style
-----

This code base will follow a minimal coding style. That is:

 * /t tabs
 
 * Curly braces on the same line as the control structure beginning
 
 * No spacing around operators
 
 * littleBig naming convention
 
 * Code that explains itself - comments should be unnecessary