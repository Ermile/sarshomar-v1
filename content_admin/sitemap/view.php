<?php
namespace content_admin\sitemap;

class view extends \mvc\view
{
	public function config()
	{
		if(\lib\utility::get('run') === 'yes')
		{
			$this->data->sitemapData = $this->model()->generate_sitemap();
		}
	}
}
?>