<?php
namespace content_admin\sitemap;
use \lib\utility;

class model extends \mvc\model
{

	public function post_sitemap()
	{
		var_dump(22);
		return 57;
	}


	function generate_sitemap()
	{
		// create sitemap for each language
		$result   = '';

		$site_url = \lib\router::get_storage('url_site');
		$result   .= "<pre>";
		$result   .= $site_url.'<br/>';
		$sitemap  = new \lib\utility\sitemap($site_url , root.'public_html/', 'sitemap' );
		$counter  =
		[
			'pages'       => 0,
			'polls'       => 0,
			'posts'       => 0,
			'helps'       => 0,
			'attachments' => 0,
			'otherTypes'  => 0,
			'terms'       => 0,
			// 'cats'        => 0,
			// 'otherTerms'  => 0,
		];

		// add list of static pages


		// add posts
		foreach ($this->model()->sitemap('posts', 'post') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'])
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.8', 'daily', $row['post_publishdate']);
			$counter['posts'] += 1;
		}

		// add poll
		foreach ($this->model()->sitemap('posts', 'poll') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'])
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.8', 'daily', $row['post_publishdate']);
			$counter['polls'] += 1;
		}

		// add pages
		foreach ($this->model()->sitemap('posts', 'page') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'])
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.6', 'weekly', $row['post_publishdate']);
			$counter['pages'] += 1;
		}

		// add helps
		foreach ($this->model()->sitemap('posts', 'helps') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'])
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.3', 'monthly', $row['post_publishdate']);
			$counter['helps'] += 1;
		}

		// add attachments
		foreach ($this->model()->sitemap('posts', 'attachment') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'])
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.2', 'weekly', $row['post_publishdate']);
			$counter['attachments'] += 1;
		}

		// add other type of post
		foreach ($this->model()->sitemap('posts', false) as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'])
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.5', 'weekly', $row['post_publishdate']);
			$counter['otherTypes'] += 1;
		}

		// add cats and tags
		foreach ($this->model()->sitemap('terms') as $row)
		{
			$myUrl = $row['term_url'];
			if($row['term_language'])
			{
				$myUrl = $row['term_language'].'/'. $myUrl;
			}


			$sitemap->addItem($myUrl, '0.4', 'weekly', $row['date_modified']);
			$counter['terms'] += 1;
		}

		$sitemap->createSitemapIndex();
		$result .= "</pre>";
		$result .= "<p class='alert alert-success'>". T_('Create sitemap Successfully!')."</p>";

		foreach ($counter as $key => $value)
		{
			$result .= "<br/>";
			$result .= $key. ": <b>". $value."</b>";
		}

		return $result;
	}



	public function sitemap($_table = 'posts', $_type = null)
	{
		$prefix = substr($_table, 0, -1);
		$status = $_table === 'posts'? 'publish': 'enable';
		$date   = $_table === 'posts'? 'post_publishdate': 'date_modified';
		$lang   = $_table === 'posts'? 'post_language': 'term_language';
		$qry    = $this->sql()->table($_table)->where($prefix.'_status', $status);
		if($_type)
		{
			$qry = $qry->and($prefix.'_type', $_type);
		}
		elseif($_type === false && $_table === 'posts')
		{
			$qry = $qry->and($prefix.'_type', '<>', "'post'");
			$qry = $qry->and($prefix.'_type', '<>', "'page'");
			$qry = $qry->and($prefix.'_type', '<>', "'help'");
			$qry = $qry->and($prefix.'_type', '<>', "'attachments'");
		}


		$qry    = $qry->field($prefix.'_url', $date, $lang)->order('id','DESC');
		return $qry->select()->allassoc();
	}


}
?>