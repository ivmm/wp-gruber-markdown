<?php
	if( isset($_POST['ratio']) ) {
		$ratio = floatval($_POST['ratio'] );
		$ratio = $ratio == 0 ? 1 : $ratio;
		update_option('gruber-markdown-ratio', $ratio);
		update_option('gruber-markdown-revise', '1');
	}

	$opt = new GruberMarkdownOption;
	$opt->revise_font_size();
	wp_register_style('gruber-markdown', plugins_url('css/gruber_markdown.css?t='. time(), __FILE__));
	wp_register_script('code-prettify', plugins_url('/js/code-prettify.js', __FILE__));
	wp_enqueue_style('gruber-markdown');
	wp_enqueue_script('code-prettify');
?>

<div class="gruber-markdown" style="width:72%; backgrounds:#ffffff;">
	<div>
		<h1>Gruber Markdown for WordPress</h1>
		<form action="<?php echo admin_url( 'admin.php?page=' . plugin_basename( __FILE__ ) ); ?>" 
				method="post" novalidate="novalidate">
			<p>
				<strong>字体显示比例：</strong>
				<input type="text" name="ratio" 
							 value="<?php echo get_option('gruber-markdown-ratio', '1'); ?>" 
							 style="margin:1em 1em;"/>
				填写大于0的小数，默认是1.0
			</p>
			<p>如果设置不起作用，可能是您的wordpress权限不够，可以尝试以下命令.</p><pre><code>chmod -R 755 wordpress
chown -R www-data.www-data wordpress</code></pre>

			<p class="submit"><input type="submit" class="button-primary" value="保存设置" /></p>
		</form>
	</div>
	<div>
		<h2>预览效果</h2>
		<h3>Markdown原文</h3>
		<pre><code><?php
$text = "# Markdown——从入门到精通
#### 导语：
>[Markdown](http://zh.wikipedia.org/wiki/Markdown) 是一种轻量级的「标记语言」，它的优点很多，目前也被越来越多的写作爱好者，撰稿者广泛使用。看到这里请不要被「标记」、「语言」所迷惑，Markdown 的语法十分简单。常用的标记符号也不超过十个，这种相对于更为复杂的HTML 标记语言来说，Markdown 可谓是十分轻量的，学习成本也不需要太多，且一旦熟悉这种语法规则，会有一劳永逸的效果。

### Markdown官方文档

>这里可以看到官方的 Markdown 语法规则文档，当然，后文我也会用自己的方式，阐述这些语法在实际使用中的用法。

* [*创始人 John Gruber 的 Markdown 语法说明*](http://daringfireball.net/projects/markdown/syntax)

* [*Markdown 中文版语法说明*](http://wowubuntu.com/markdown/#list)

### 使用Markdown的优点

* 专注你的文字内容而不是排版样式。
* 轻松的导出 HTML、PDF 和本身的 .md 文件。
* 纯文本内容，兼容所有的文本编辑器与字处理软件。
* 可读，直观。适合所有人的写作语言。

### 我该用什么工具？

![Mou icon](http://mouapp.com/Mou_128.png)

在 Mac OS X 上，我强烈建议你用[Mou](http://mouapp.com)这款免费且十分好用的 Markdown 编辑器，它支持实时预览，既左边是你编辑 Markdown 语言，右边会实时的生成预览效果，笔者文章就是 Mou 这款应用写出来的。";
echo $text;
?>
		</code></pre>
		<h3>生成Html页面</h3>
		<div style="padding:20px; background:#ffffff">
			<?php
			echo (new Parsedown())->text($text);
			?>
			
		</div>
	</div>
</div>