[![GPLv3 License](https://img.shields.io/badge/License-GPL%20v3-yellow.svg)](https://opensource.org/licenses/) ![GitHub Tag](https://img.shields.io/github/v/tag/vincentbitter/football-club-manager?label=Plugin)

# Football Club Manager

![Football Club Manager](docs/img/banner-1280x731.png)

With Football Club Manager it is easy to manage your amateur football club in WordPress. Create team pages, player info, and integrate match data with just a few clicks!

---

## 🚀 Features

- 🏟️ Create customizable team pages
- 👤 Register team players, including picture
- 📅 Publish match schedule and results
- 🙋‍♂️ Manage volunteers
- 📝 New players and volunteers can sign up
- 🧩 Customizable blocks for Gutenberg editor
- 🌍 Multilingual support

---

## 📋 Requirements
- WordPress 6.8 or higher
- PHP 7.4 or higher

---

## 📦 Installation

You can install the plugin in one of three ways:

### Option 1: Install from official WordPress Plugin Directory
1. In your WordPress dashboard, go to **Plugins → Add New**.
2. Search for **Football Club Manager** and click **Install now**, then **Activate**.

### Option 2: Download a Release

1. Visit the [Releases page](https://github.com/vincentbitter/football-club-manager/releases).
2. Download the latest `.zip` file.
3. In your WordPress dashboard, go to **Plugins → Add New → Upload Plugin**.
4. Upload the `.zip` file and activate the plugin.

### Option 3: Clone the Repository (development version)

1. Open your terminal and run:
   ```
   git clone https://github.com/vincentbitter/football-club-manager.git
   ```
2. Upload the cloned folder to your WordPress `/wp-content/plugins/` directory.
3. Activate the plugin via the WordPress admin dashboard.

---

## 🏃 Usage

- Navigate to **Football Club Manager** in your WordPress dashboard.
- Add **Players**, **Teams**, and **Matches**.
- Visit team pages and modify the content, or enable/disable default blocks via page settings.
- Go to the **Football Club Manager** section in **Customizer** to change default blocks on team page.
- Display match results or team rosters on your site using Gutenberg editor.

---

## 📦 Add-ons

Football Club Manager is designed to be extensible.
Currently available:

- 🔗 [FCM for Sportlink](https://github.com/vincentbitter/fcm-for-sportlink)  
  Integrates Football Club Manager with **Sportlink**, allowing you to synchronize official match data and player information directly into WordPress.

---

## 📜 Changelog

See what’s new in each release on the [Changelog page](https://github.com/vincentbitter/fcm-for-sportlink/blob/main/CHANGELOG.md) or on the [Releases page](https://github.com/vincentbitter/fcm-for-sportlink/releases).

---

## ❓ FAQ

❔ **Do I need coding experience to use this plugin?**  
Not at all! You just need access to your WordPress dashboard to manage the data, and you can use the Gutenberg editor to manage content.

❔ **Can I also import data?**
Yes! Since Football Club Manager is built using Custom Post Types, it's compatible with most import/export plugins. Even WordPress itself offers import/export features, which you can find in the **Tools** admin menu. Also, you can develop your own import plugin for automatic importing, like the one for [Sportlink](https://github.com/vincentbitter/fcm-for-sportlink).

❔ **Do you collect any data from me?**
No. Football Club Manager does not contain any tracking code or integration with external services. WordPress Plugin Directory tracks the number of installs though, but you can avoid even that by downloading a release from GitHub.

❔ **Is this plugin compatible with other WordPress themes or plugins?**
It’s designed to work with Gutenberg editor. While it should play nicely with most themes, custom styling may be needed depending on your setup.

❔ **What is Gutenberg editor?**
The standard page editor since WordPress 5.0 (December 2018). If you use a different editor, blocks might not work. Check the [demo](https://wordpress.org/gutenberg/) if you are not sure if your website uses it.

❔ **How do I add a signup form for new players or volunteers?**
You can add signup forms in the Gutenberg editor and customize those to your needs. The Signup Form block contains properties to chose between Player and Volunteer signup, and you can even specify a subtype if you need different forms for different types of players or volunteers. When adding a Signup Form block to the page, all possible child blocks will automatically be added. You can remove the blocks you don't need or customize and reorganize those.

❔ **What happens if someone signs up via the signup form?**
The data is stored as a new 'signup' you can find in the Wordpress admin area (Football Club Manager -> Signups). Because of GDPR and other privacy/security regulations, it is not advised to store all personal data in Wordpress for a long time. Instead, you can verify the registration and copy the data to a separate CRM system. After processing the data, remove it from Wordpress. Need automation? Create a plugin for Football Club Manager that forwards the signup data to your CRM system.

---

## 🤝 Contributing

Found a bug or have a feature request?  
Please open an issue on [GitHub](https://github.com/vincentbitter/football-club-manager/issues).

Want to contribute? Fork the repo and submit a pull request — all help is welcome!

---

## 📄 License

This plugin is licensed under the [GNU GPL v3](https://www.gnu.org/licenses/gpl-3.0.en.html).