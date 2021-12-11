<?php

namespace Oblik\KirbyVite;

use Kirby\Http\Server;
use Kirby\Http\Uri;

class Vite
{
	protected static $instance;

	public static function instance()
	{
		return static::$instance ?? (static::$instance = new static());
	}

	public $manifest;

	public function __construct()
	{
		$path = implode(DIRECTORY_SEPARATOR, [
			kirby()->root(),
			option('oblik.vite.build.outDir'),
			'manifest.json'
		]);

		try {
			$this->manifest = json_decode(file_get_contents($path), true);
		} catch (\Throwable $t) {
			// Vite is running in development mode.
		}
	}

	public function prodUrl(string $path)
	{
		return implode('/', [
			site()->url(),
			option('oblik.vite.build.outDir'),
			$path
		]);
	}

	public function devUrl(string $path)
	{
		$uri = new Uri([
			'scheme' => option('oblik.vite.server.https') ? 'https' : 'http',
			'host'   => Server::host(),
			'port'   => option('oblik.vite.server.port'),
			'path'   => $path
		]);

		return $uri->toString();
	}

	public function js(string $entry)
	{
		if (is_array($this->manifest)) {
			$url = $this->prodUrl($this->manifest[$entry]['file']);
		} else {
			$url = $this->devUrl($entry);
		}

		return js($url, ['type' => 'module']);
	}

	public function css(string $entry)
	{
		if (is_array($this->manifest)) {
			foreach ($this->manifest[$entry]['css'] as $file) {
				return css($this->prodUrl($file));
			}
		}
	}
}