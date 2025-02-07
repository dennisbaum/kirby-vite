# Kirby Vite Plugin

Plugin for Kirby that allows you to load assets generated by [Vite](https://vitejs.dev/).

- In development mode, assets are loaded from Vite's development server, allowing you to benefit from all of its features, such as HMR.
- In production mode, URLs to the built assets are provided and they're served by PHP.

Another similar plugin is [arnoson/kirby-vite](https://github.com/arnoson/kirby-vite), but it's more complicated and relies on the generation of a `.lock` file that Vite no longer seems to generate. Thus, it inspired the creation of this plugin.

**Note:** Developed and tested with [Vite `2.7.1`](https://github.com/vitejs/vite/tree/v2.7.1).

## How it works

The plugin depends on Vite's [manifest functionality](https://vitejs.dev/config/#build-manifest), which gives the generated production assets' location. If such a file exists, the plugin attempts to load the files listed inside. If this file doesn't exist, it's assumed that Vite is running in development mode and the plugin attempts to request files from Vite's develpment server.

## How to use

### Install

You can find [`oblik/kirby-vite` on packagist](https://packagist.org/packages/oblik/kirby-vite):

```
composer require oblik/kirby-vite
```

### Enable manifest

Make sure you've enabled the [`build.manifest`](https://vitejs.dev/config/#build-manifest) option in your `vite.config.js`:

```js
export default {
	build: {
		manifest: true,
	},
};
```

### Output JavaScript

You need to pass your entry script to the `js()` method:

```php
<?= vite()->js('src/index.js') ?>
```

### Output CSS

You have to do the same as for JavaScript, but use the `css()` method:

```php
<?= vite()->css('src/index.js') ?>
```

**Note:** In Vite, you import CSS in your main JavaScript file. In order to find the CSS from the generated manifest, the plugin needs the file that _imports_ your CSS, not the CSS file itself.

**Note 2:** In development mode, the `css()` method does nothing because Vite automatically loads your CSS when your JavaScript loads, as well as the required `@vite/client` script. In other words, `vite()->js()` would load everything.

### Remove generated assets

As explained in [the how it works section](#how-it-works), to ensure proper function, you should remove the build folder every time you start your development server. This can easily be done with `rm -rf` in your npm dev script:

```json
{
	"scripts": {
		"dev": "rm -rf dist && vite"
	}
}
```

There's an [open issue](https://github.com/vitejs/vite/issues/6055) in the Vite repo that suggests this as a core feature of the framework, which you can upvote. Until then, we'll have to take care of removing that folder ourselves.

## Options

In your `config.php`, make sure you configure the plugin to match your `vite.config.js`:

```php
return [
	'oblik.vite' => [
		'server' => [
			'port' => 3000,
			'https' => false,
		],
		'build' => [
			'outDir' => 'dist'
		]
	]
];
```

**Note:** The values above are the default ones, so if they match your setup, you don't need to configure anything. 🤙
