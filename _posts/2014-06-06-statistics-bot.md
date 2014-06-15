---
layout: post
title:  "StatsBot: A communal IRC statistics bot"
date:   2014-06-06 09:20:22
categories: irc statistics
---

![The Statistics bot in action](/images/stats.png)

Those familiar with IRC have probably seen Rizon's Stats bot in action. That bot will join a channel if commanded to do so by the channel founder and generate statistics using [pisg](http://pisg.sourceforge.net).

StatsBot is similar to Rizon's Stats, except I wrote it in only 143 lines of code and joins a channel if /invited by an op. It logs stats about the channel in energymech format and uses pisg to generate channel statistics. The bot is live on [Snoonet](https://snoonet.org) under the nick "Statistics", and it generates stats every twenty minutes.

[Source code on GitHub](https://github.com/flotwig/StatsBot/)  
[Example of generated stats for #reddit](http://stats.irc.so/%23reddit.html)