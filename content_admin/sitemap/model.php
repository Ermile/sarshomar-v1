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

		// --------------------------------------------- Static pages

		// add list of static pages
		$sitemap->addItem('fa', '1', 'daily');

		$sitemap->addItem('$', '0.9', 'daily');
		$sitemap->addItem('api', '1', 'daily');
		$sitemap->addItem('help', '0.8', 'daily');
		$sitemap->addItem('changelog', '0.8', 'daily');

		$sitemap->addItem('about', '0.8', 'weekly');
		$sitemap->addItem('contact', '0.8', 'weekly');
		$sitemap->addItem('terms', '0.8', 'weekly');
		$sitemap->addItem('privacy', '0.8', 'weekly');
		$sitemap->addItem('social-responsibility', '0.8', 'weekly');
		$sitemap->addItem('benefits', '0.8', 'weekly');
		$sitemap->addItem('ref', '0.5', 'monthly');
		$sitemap->addItem('gift', '0.4', 'monthly');
		$sitemap->addItem('logo', '1', 'monthly');

		// add all targets
		$sitemap->addItem('target/organizations-and-companies', '0.8', 'weekly');
		$sitemap->addItem('target/universities-and-research-institutes', '0.8', 'weekly');
		$sitemap->addItem('target/agencies', '0.8', 'weekly');
		$sitemap->addItem('target/developers-and-webmasters', '0.8', 'weekly');

		// add all benefits
		$sitemap->addItem('benefits/guest', '0.8', 'weekly');
		$sitemap->addItem('benefits/multimedia-questions', '0.7', 'weekly');
		$sitemap->addItem('benefits/results-output', '0.7', 'weekly');
		$sitemap->addItem('benefits/graphic-results', '0.7', 'weekly');
		$sitemap->addItem('benefits/essay-type-questions', '0.7', 'weekly');
		$sitemap->addItem('benefits/language-localization', '0.7', 'weekly');
		$sitemap->addItem('benefits/without-disturbance', '0.7', 'weekly');
		$sitemap->addItem('benefits/social-networks', '0.7', 'weekly');
		$sitemap->addItem('benefits/target-statistical-population', '0.7', 'weekly');
		$sitemap->addItem('benefits/question-charts', '0.7', 'weekly');
		$sitemap->addItem('benefits/results-personalization', '0.7', 'weekly');
		$sitemap->addItem('benefits/easy-access', '0.7', 'weekly');
		$sitemap->addItem('benefits/valid-population', '0.7', 'weekly');
		$sitemap->addItem('benefits/knowledge-base', '0.7', 'weekly');

		$sitemap->addItem('election', '0.2', 'monthly');
		$sitemap->addItem('election/iran', '0.3', 'monthly');
		$sitemap->addItem('election/iran/president', '0.4', 'monthly');
		$sitemap->addItem('election/iran/president/1396', '1', 'hourly');
		$sitemap->addItem('election/iran/president/1392', '0.8', 'weekly');
		$sitemap->addItem('election/iran/president/1388', '0.8', 'weekly');
		$sitemap->addItem('election/iran/president/1384/2', '0.7', 'weekly');
		$sitemap->addItem('election/iran/president/1384', '0.7', 'weekly');
		$sitemap->addItem('election/iran/president/1380', '0.7', 'weekly');
		$sitemap->addItem('election/iran/president/1376', '0.7', 'weekly');
		$sitemap->addItem('election/iran/president/1372', '0.7', 'weekly');
		$sitemap->addItem('election/iran/president/1368', '0.7', 'weekly');
		$sitemap->addItem('election/iran/president/1364', '0.7', 'weekly');
		$sitemap->addItem('election/iran/president/1360/2', '0.7', 'weekly');
		$sitemap->addItem('election/iran/president/1360', '0.7', 'weekly');
		$sitemap->addItem('election/iran/president/1358', '0.7', 'weekly');




		// PERSIAN
		// add static pages of persian
		$sitemap->addItem('fa/$', '0.9', 'daily');
		$sitemap->addItem('fa/api', '1', 'daily');
		$sitemap->addItem('fa/help', '0.8', 'daily');
		$sitemap->addItem('fa/changelog', '0.8', 'daily');

		$sitemap->addItem('fa/about', '0.8', 'weekly');
		$sitemap->addItem('fa/contact', '0.8', 'weekly');
		$sitemap->addItem('fa/terms', '0.8', 'weekly');
		$sitemap->addItem('fa/privacy', '0.8', 'weekly');
		$sitemap->addItem('fa/social-responsibility', '0.8', 'weekly');
		$sitemap->addItem('fa/benefits', '0.8', 'weekly');
		$sitemap->addItem('fa/ref', '0.5', 'monthly');
		$sitemap->addItem('fa/gift', '0.4', 'monthly');
		$sitemap->addItem('fa/logo', '1', 'monthly');


		// add all benefits
		$sitemap->addItem('fa/benefits/guest', '0.8', 'weekly');
		$sitemap->addItem('fa/benefits/multimedia-questions', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/results-output', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/graphic-results', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/essay-type-questions', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/language-localization', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/without-disturbance', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/social-networks', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/target-statistical-population', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/question-charts', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/results-personalization', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/easy-access', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/valid-population', '0.7', 'weekly');
		$sitemap->addItem('fa/benefits/knowledge-base', '0.7', 'weekly');

		// add all targets
		$sitemap->addItem('fa/target/organizations-and-companies', '0.8', 'weekly');
		$sitemap->addItem('fa/target/universities-and-research-institutes', '0.8', 'weekly');
		$sitemap->addItem('fa/target/agencies', '0.8', 'weekly');
		$sitemap->addItem('fa/target/developers-and-webmasters', '0.8', 'weekly');


		$sitemap->addItem('fa/election', '0.2', 'monthly');
		$sitemap->addItem('fa/election/iran', '0.3', 'monthly');
		$sitemap->addItem('fa/election/iran/president', '0.4', 'monthly');
		$sitemap->addItem('fa/election/iran/president/1396', '1', 'hourly');
		$sitemap->addItem('fa/election/iran/president/1392', '0.8', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1388', '0.8', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1384/2', '0.7', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1384', '0.7', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1380', '0.7', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1376', '0.7', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1372', '0.7', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1368', '0.7', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1364', '0.7', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1360/2', '0.7', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1360', '0.7', 'weekly');
		$sitemap->addItem('fa/election/iran/president/1358', '0.7', 'weekly');



		// add posts
		foreach ($this->model()->sitemap('posts', 'post') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'] && $row['post_language'] !== 'en')
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
			if($row['post_language'] && $row['post_language'] !== 'en')
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			if(isset($row['post_privacy']) && $row['post_privacy'] === 'public')
			{
				$sitemap->addItem($myUrl, '0.8', 'daily', $row['post_publishdate']);
				$counter['polls'] += 1;
			}
		}

		// add pages
		foreach ($this->model()->sitemap('posts', 'page') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'] && $row['post_language'] !== 'en')
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
			if($row['post_language'] && $row['post_language'] !== 'en')
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
			if($row['post_language'] && $row['post_language'] !== 'en')
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
			if($row['post_language'] && $row['post_language'] !== 'en')
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
			$qry = $qry->and($prefix.'_type', '<>', "'poll'");
			$qry = $qry->and($prefix.'_type', '<>', "'survey'");
			$qry = $qry->and($prefix.'_type', '<>', "'page'");
			$qry = $qry->and($prefix.'_type', '<>', "'help'");
			$qry = $qry->and($prefix.'_type', '<>', "'attachment'");
		}

		if($_table === 'posts')
		{
			$qry = $qry->field($prefix.'_url', $date, $lang, 'post_privacy')->order('id','DESC');
		}
		else
		{
			$qry = $qry->field($prefix.'_url', $date, $lang)->order('id','DESC');
		}

		return $qry->select()->allassoc();
	}


}
?>