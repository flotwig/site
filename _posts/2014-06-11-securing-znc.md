---
layout: post
title:  "Securing ZNC with a SSL certificate"
date:   2014-06-11 12:51:43
tags: irc znc netsec
---

[ZNC](http://znc.in/), the dominant IRC bouncer, has an option to use an SSL certificate to encrypt your connection between your PC and the bouncer. The hardest part of setting this up is creating a self-signed certificate to use for the encryption. The commands to generate and assemble a 10-year SSL certificate with 4096 bits of security are below.  
{% highlight console %}
cd ~/.znc
openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -days 3650 -nodes
cat key.pem > znc.pem
cat cert.pem >> znc.pem
{% endhighlight %}  
Make sure to set your Common Name (CN) equal to the hostname of your server. Everything else can be left blank as this certificate is just for your personal usage.  
Once you have a correctly formatted znc.pem in your ~/.znc directory, enable SSL on your ZNC's interface by either modifying the ZNC config file and using /znc rehash or by using *controlpanel. ZNC will automatically use the znc.pem you generated for security.