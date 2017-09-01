<?php
/*
Plugin Name: Gruber Markdown
Plugin URI: https://github.com/flyhttp/wp-gruber-markdown
Description: Automatically convert markdown content to html page
Version: 1.0
Author: Neo
Author URI: http://blog.iflappy.com
License: Apache License Version 2.0
*/

require_once 'Parsedown.php';


class GruberMarkdown
{
	function __construct()
	{
		/* 注册激活插件时要调用的函数 */ 
		// register_activation_hook( __FILE__, 'gruber-markdown_install');   

		/* 注册停用插件时要调用的函数 */ 
		// register_deactivation_hook( __FILE__, 'gruber-markdown_remove' );

		/* 禁止文章内容转码 */
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_content', 'wptexturize' );

		/* 禁止文章可视化编辑 */
		add_filter( 'user_can_richedit','__return_false' );

		// add_filter( 'the_title',  array($this, 'the_title') );

		/* 将markdown文章转换为html */
		add_filter( 'the_content',  array($this, 'the_content') );
		add_action( 'wp_enqueue_scripts',  array($this, 'wp_enqueue_scripts') ); 
		add_action( 'get_footer', array($this, 'get_footer') );

		if( is_admin() ) {
		    /*  利用 admin_menu 钩子，添加菜单 */
		    add_action('admin_menu', array($this, 'admin_menu') );
		}
	}

	function get_footer(){
		?>
			 <script type="text/javascript">
				var pre = document.getElementsByTagName('pre');
				for (var i=0;i<pre.length;i++)
				{
					pre[i].className = "prettyprint";
				}
				// var b = document.getElementsByTagName('body');
				// b.onload="prettyPrint()";
				prettyPrint();
			</script> 
		<?php
	}
	

	function wp_enqueue_scripts() {
		$opt = new GruberMarkdownOption;
		$opt->revise_font_size();
		$css = $this->revise == 0 ? 'gruber_markdown.css' : 'gruber_markdown.css?t='.time();
		wp_register_style('gruber-markdown', plugins_url($css, __FILE__));
		wp_enqueue_style('gruber-markdown');

		$prettify_js = 'prettify.js' ;
		wp_register_script('google-code-prettify-js', plugins_url($prettify_js, __FILE__));
		wp_enqueue_script('google-code-prettify-js');

		$prettify_css = 'prettify.css' ;
		wp_register_style('google-code-prettify-css', plugins_url($prettify_css, __FILE__));
		wp_enqueue_style('google-code-prettify-css');
	}

	function the_title( $title ) {
		$f = fopen('aaa.txt', 'a+');
		fwrite($f, "\n\ntitle\n");
		fwrite($f, $title);
		fclose($f);
		return $title;
	}

	function the_content( $content ) {
		$parsedown = new Parsedown();
		$content = $parsedown->text($content);
		$content = '<div class="gruber-markdown">' . $content . '</div>';
		return $content;
	}

	function admin_menu(){
		add_options_page(
			'Set Gruber Markdown', 'Gruber Markdown', 'manage_options', 'wp-gruber-markdown/gruber_markdown_options.php');
	}
}

class GruberMarkdownOption
{
	var $ratio;
	var $revise;
	var $unit;
	
	function __construct($unit = 'px')
	{
		$this->unit = $unit;
	}

	function revise_font_size() {
		$ratio = get_option('gruber-markdown-ratio');
		$revise = get_option('gruber-markdown-revise');
		if($ratio == null || '' === $ratio) {
			add_option('gruber-markdown-ratio', '1.0');
			$this->ratio = 1.0;
		}else{
			$this->ratio = floatval($ratio);
		}

		if($revise == null || '' === $revise){
			$this->revise = 1;
			add_option('gruber-markdown-revise', '0');
		}else{
			$this->revise = intval($revise);
			update_option('gruber-markdown-revise', '0');
		}

		$bac_path = dirname(__FILE__) . '/gruber_markdown.css.' . $this->unit;
		$css_path = dirname(__FILE__) . '/gruber_markdown.css';

		if(file_exists($css_path) && $this->revise == 0)
			return;

		$bac = file_get_contents($bac_path);
		$pattern = '/(\d*\.*\d+)' . $this->unit . '/';
		$css = preg_replace_callback(
			$pattern, 
			function($v){
				return (floatval($v[0]) * $this->ratio) . $this->unit;
			}, 
			$bac);
		file_put_contents($css_path, $css);
	}
}

new GruberMarkdown;

?>