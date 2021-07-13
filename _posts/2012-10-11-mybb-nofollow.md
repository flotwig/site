---
layout: post
title:  "Adding rel=nofollow to outgoing links in MyBB"
date:   2012-10-11 00:00:00
tags: mybb
---

 If you run a forum, you probably have spammers signing up all the time, posting links on your site and stealing your valuable PageRank. MyBB offers a way to add rel="nofollow" to outgoing links in posts via a creative use of the MyCode functionality.

To add rel="nofollow" to outgoing links, first go to your admin panel in MyBB. Visit the Configuration tab, and then select MyCode on the left sidebar. We'll be adding two new MyCode. Click "Add New MyCode" and fill it out like so:

* **Title:** Nofollow
* **Regular Expression:** href="http([s]?)://(.\*?)"
* **Replacement:** href="http$1://$2" rel="nofollow"
* **Enabled:** Yes
* **Parse order:** 99

This MyCode will go through every link in a post and add rel="nofollow" to them all. It will even add rel="nofollow" to links which point to your own website, which is probably not what you want! To avoid this, add another MyCode which will strip rel="nofollow" from local links:

* **Title:** Dofollow
* **Regular Expression:** href="http([s]?)://(|www\.)example.com(.\*?)" rel="nofollow"
* **Replacement:** href="http$1://$2example.com$3"
* **Enabled:** Yes
* **Parse order:** 100

Replace example.com with your actual domain name. This MyCode will strip rel="nofollow" from www.example.com and example.com links. If you want to add multiple domains, you do not have to create multiple MyCodes! Just replace "example.com" in the Regular Expression with "(domain1.com|domain2.net|domain3.org)" and replace "example.com$3" in the Replacement with "$3$4".

There you go! Just a few quick steps to protecting your PageRank against spammers, without any nasty code modifications or plugins. :)