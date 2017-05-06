<?php
namespace content_admin\sitemap;

class view extends \content_admin\main\view
{
	public function config()
	{
		$this->data->page['title']   = T_('Sitemap');

		if(\lib\utility::get('run') === 'yes')
		{
			$this->data->sitemapData = $this->model()->generate_sitemap();
		}
	}
}
?>