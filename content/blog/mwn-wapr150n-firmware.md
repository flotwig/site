 Today, I set out on a quest to try and decrypt/decompress/dearchive the firmware of my MediaLink MWN-WAPR150N router. I would put DD-WRT on it, but the hardware is too limited for that sort of junk. It's a fine Wireless-N router as it is, but the inner programmer in me wanted to get inside and see what damage I could do. I especially wanted to mess with the web front-end, which is coded in ASP and runs on GoAhead-Webs, a web server for embedded systems. This is a log of my adventures with the MediaLink APR150N wireless router.

I set out by downloading the latest (as of July 8, 2012) firmware .zip from MediaLink's website. I installed it on my router, and according to the web front-end, the firmware's version string is: (deep breath, get ready for it) "MWN-WAPR150N_FirmwareUpdate_v11.8".

The .bin file which contains the actual firmware stumped me, at first. I couldn't figure out the file format anywhere online and a hex editor showed it was all binary mishmash with no human-readable strings. Luckily for me, the user "trip" on #chat on EFnet told me how to get into that sweet sweet firmware goodness. He told me that the firmware .bin was a filesystem image - uImage, to be precise. I found [this wiki page](http://buffalo.nas-central.org/wiki/How_to_Extract_an_uImage), which contains a shell script capable of extracting data from uImage files.

Using that shell script, I extracted an image file. Once again the file was binary mish-mash with no human-readable strings, but this time 7-zip was able to open the file without errors. I was excited, for a moment - progress was finally being made. My excitement quickly waned once I saw that the image extracted from the .bin was but a compressed LZMA:25 archive of a 3,484 kilobyte file.

This new development wasn't such a bust in the end - opening the extracted contents of the LZMA archive in my hex editor of choice showed a good amount of router-related code. Here's a small sample:
 

    Ralink Wireless Access Point....RT2860..12345678....WFA-SimpleConfig-Registrar-1-0..WFA-SimpleConfig-Enrollee-1-0...SSID=...WPAPSK=.SelReg:.%s%c....goahead.(WscEAPAction)Elem->MsgLen..Key%dStr%d=.Key%dType=..Default.....SSID1=..SSID2=..SSID3=..SSID4=..Auth


Happy day! Finally, I was getting somewhere. Unfortunately, I still didn't know much about the structure of the image and editing it at this stage would likely brick my router if I were to upload a modified image. So I pushed onward in an attempt to find out what file format the firmware image was in.

After a while, I found a neat little Windows application called TrID which uses a small database of info to quickly identify the correct filetype of an unknown file. I gleefully downloaded the application and the 612 kilobyte definitions file and ran it on the extracted uImage and the extracted LZMA archive. The uImage was unidentifiable, but the LZMA innards had partial matches to five different file formats:
 

    TrID/32 - File Identifier v2.10 - (C) 2003-11 By M.Pontello 
    Definitions found:  4791 
    Analyzing... Collecting data from file: Image~ 
    40.2% (.ATN) Photoshop Action (5007/6/1) 
    16.1% (.WK*) Lotus 123 Worksheet (generic) (2005/4)  
    9.0% (.GMC) Game Music Creator Music (1130/43) 
    8.2% (.) MacBinary 1 header (1029/4)  
    8.0% (.TGA) Targa bitmap (Original TGA Format - No Image ID) (1007/3)



Out of those five file formats, only one really makes sense. You can't exactly have a Lotus Notes worksheet or a music file as your firmware.
I searched the Internet for "MacBinary". MacBinary is a file format used by Macs to create disk images. I don't own a Mac, but I found an app for Windows named DeMacBin which is capable of extracting MacBinary filesystems to plain ole' files. Running it on the extracted image file didn't work as well as I had expected, getting a "File is not MacBinary" error.

I'm stuck, now. I have no idea what filetype the extracted image is and I have no idea about how to find that information out. Any help would be greatly appreciated. :)

 

EDIT: Update on the sitch. The MWN-WAPR150N is definitely running on Linux, Linux kernel strings and references to initrd are scattered all around the firmware. It is also a clone of the Tenda W268R device manufactured by "SHENZHEN TENDA TECHNOLOGY CO.,LTD", according to the FCC documents.

The WiFi is powered by a Ralink RT2860 module, it appears to be pretty generic. The CPU makes use of MIPS. The .bin described in the article runs (in the technical sense of the term) in QEMU without much finagling. I plan on calling MediaLink tomorrow to ask them about the processor, hardware specs, the bootloader and maybe even to try and snag some source code.