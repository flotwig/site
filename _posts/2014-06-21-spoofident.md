---
layout: post
title:  "spoofident: A fake identd written in Python"
date:   2014-06-21 20:27:25
categories: netsec
---

![The workhorse function of spoofident](/images/spoofident.png)
Many protocols such as IRC require or strongly suggest the use of an [ident](http://tools.ietf.org/html/rfc1413) daemon to prove that you are who you say you are, or to hold you accountable for your actions. An identd is supposed to respond to queries as to which user is using which port; however, this information can be potentially dangerous. A real identd allows attackers to gain information about your system - usernames, active ports, even a fingerprint of your active operating system. The RFC linked above even cites these vulnerabilities.

I had a need to run an ident server; however, I am wary of creating unnecessary security holes in my server. That's why I wrote [spoofident](https://github.com/flotwig/spoofident). spoofident is a daemon written in Python which provides a custom username/OS response to all incoming ident queries. It is dual-stack (meaning that it runs on both IPv4 and IPv6) and written to consume little resources, less than [oidentd](http://en.wikipedia.org/wiki/Oidentd). I suggest using it if you are in a situation where you need to provide ident but refuse to compromise the security of your systems for that functionality.

[GitHub repo for spoofident](https://github.com/flotwig/spoofident)

[README for spoofident](https://github.com/flotwig/spoofident/blob/master/README.md)