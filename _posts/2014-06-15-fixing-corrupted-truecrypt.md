---
layout: post
title:  "Fixing a corrupted TrueCrypt volume for Windows rescue"
date:   2014-06-15 12:19:56
categories: truecrypt encryption
---

![TrueCrypt rescue disk screenshot.](/images/truecrypt.png)

Like an idiot, I held down the power button to reboot my machine. It booted back up, and once I entered my TrueCrypt full-disk encryption password, I was greeted with a Windows rescue and repair boot screen. I managed to create a system volume which was both encrypted and corrupted, meaning that Windows rescue could not read the volume. Instead, it asked me to insert a driver disk for my hard drive so it could attempt rescue. If I entered the command line and used DISKPART to try to list volumes, it displayed C: as filesystem type RAW.

Not a good thing.

Happily enough, this is an issue with a simple solution. Insert your TrueCrypt rescue disk which you created when you originally encrypted your system and reboot. Press F8 for "Repair Options" when you get to the TrueCrypt authentication screen, and then press 1 to permanently decrypt your system disk. At this point, you'll need to enter your decryption key, and decryption will begin. On a 500GB laptop drive, this process took over 8 hours to complete (!), so here are some other options you could try *before attempting to decrypt the drive*:

 + Create a bootable live Linux thumb drive, install TrueCrypt onto it, and mount your system volume. From there, you can use any of the Windows repair tools available on Linux or do a chkdsk or what have you. TrueCrypt on Linux cannot mount partially decrypted volumes, so if you want to go this route, be sure not to begin decryption through the TrueCrypt rescue disk
 + Create a bootable WinPE drive and use it to repair Windows. I did not try this one so I do not know if TrueCrypt can handle partially decrypted volumes on WinPE.
 + Manage to get TrueCrypt running within the Windows rescue environment so the volume is mountable and rescuable. Windows rescue kills it, saying that "This image cannot run because the appropriate subsystem is not loaded." This happens because Windows rescue is not running a full-fledged copy of Windows, so the subsystem is not available. If you have an afternoon to spare for fun Windows finagling, this is the option for you.