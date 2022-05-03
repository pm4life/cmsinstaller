# Magento 2 Cms Installer

#### Why? Because why not.

Usually when developing new or maintaining project there is a need for managing cms content.
This extension provides an option to export cms content to a `.html` which can be added to project repository
and moved easily from local to development or production environment.

## Installation:
Download package from git or install it via composer with:
```
composer require pm4life/cmsinstaller
php bin/magento setup:upgrade
```
**Next, enable module either via console command:**
```
php bin/magento config:set cms/installer/is_enabled 1
```
**or by settings in admin**: Admin > Stores > Configuration > General > Content Management > **Cms Content Installer**

![Screenshot from 2022-05-03 10-03-20](https://user-images.githubusercontent.com/47811189/166421438-a41d5408-15f4-4773-af5c-d6a4029167d1.png)

## Configuration
There are two options for cms templates, they can either be placed within `app/design/frontend/cms_install` directory, or within certain allowed modules, this is controlled by admin setting, by default when extension is installed it is set to use `app/design/frontend/cms_install` directory as template base.

By switching **Install Template Base** to `Allowed Module(s) Directory` you have option to select any **enabled module** from `app/code` space

![Screenshot from 2022-05-03 10-10-19](https://user-images.githubusercontent.com/47811189/166422369-d1b0b2b2-4442-458a-bd9d-c2262f4e0c91.png)

The idea is to present options for developer to choose if for any reason it is more convenient to have them placed all at one location per project, or alternatively have templates separated per module within local space.

## Usage

### Template preparation and export

After editing on cms page or block is completed in local environment, these can be exported to make them available for moving from environment.

To create template exports there are two ways, first one with console command:
```
php bin/magento cms:installer:export --type block --identifier 'catalog_events_lister,new_block_file'
```
Where two parameters are passed `--type` represents type of entity that is being exported, and `--identifier` represents unique identifiers of given page/cms block.

If command is called without parameters it will export all available cms blocks and pages.

Besides this way, when application is in **developer** or **default** mode, a button is available in admin panel on cms page and block forms, which generates template export that can be placed in apropriate filesystem location depending on configuration settings from above.

![Screenshot from 2022-05-03 10-45-12](https://user-images.githubusercontent.com/47811189/166426407-d9830ada-8d60-43a0-987c-347951d4c8df.png)

### Installing changes from templates to system

To apply changes from template files on remote environment run:
```
php bin/magento cms:installer:apply
```

Changes to template files are tracked, so if you add changes to template file and run `cms:installer:apply` command, that will result in given block or page being updated with those changes.
