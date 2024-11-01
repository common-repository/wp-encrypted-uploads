=== WP Encrypted Uploads ===
Contributors: ahmedgeek
Tags: encryption, uploads, secure, files, AES
Requires at least: 4.5
Tested up to: 6.5
Requires PHP: 5.6
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shield your sensitive files with unbreakable encryption! 🛡️  Say goodbye to prying eyes and take full control of your private data.

== Description ==

**WP Encrypted Uploads puts your files under lock and key! 🔒 Upgrade your WordPress file security with cutting-edge AES-128 encryption.**

**This plugin's got your back, covering a wide range of file types:**

* Images 🖼️
* Audio 🎶
* Video 🎬
* PDFs 📚
* ZIPs 🗜️

**You're the boss! 😎 Customize encryption for each file type and decide which Roles get the keys to the kingdom.**

**Lightning-fast encryption, even for those giant files? Yes, please! 🚀 This plugin uses PHP output streams for smooth, memory-friendly file serving.**

## Features

* **Your files get the VIP treatment with AES-128 encryption.** 😎 Think of it like a super-secure vault for your data.
* **Control who sees what!** 🧐 Tailor access permissions with WordPress roles – you decide who has the keys.
* **Say goodbye to sneaky peeks!** 👀  Force downloads for those images, videos, and PDFs - no more casual browsing.
* **Big files, no problem!** ⚡  This plugin handles encryption at lightning speed, even for those hefty uploads.
* **Security is its middle name.** 🔒  Rest assured, decrypted files never hang around on your server. Temporary files are used for downloads, then *poof!* they're gone.



== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The plugin will automatically create the custom upload directory inside wp-content.
4. The plugin will automatically create 16 byte AES key for encryption.

== Screenshots ==

1. The settings page.

== Changelog ==

= 1.0.1 =
* Improvements: The plugin now supports PHP 8+.
* Bug Fix: Sites running on web servers other than Apache no longer have problem downloading files.
* Bug Fix: Uploading files will no longer cause an undefined array key error.

= 1.0 =
* Initial release.