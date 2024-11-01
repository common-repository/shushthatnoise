<?php
/**
Plugin Name: ShushThatNoise - Ignorant Comment Hider
Plugin URI: http://wordpress.org/extend/plugins/shushthatnoise/
Description: Hide unwanted comments without deleting them. Edit the comment and wrap the offending text in <strong>[shush][/shush]</strong>. Example: [shush]FIRST!!![/shush] or <strong>[shush reason="Boring"]</strong>FIRST!!!<strong>[/shush]</strong> Readers can choose to read the buried comment by clicking on the "Show" link. (<em>Bonus:</em> You can also use this shortcode in your posts as a "Spoiler" hider.)
Author: Zing-Ming
Version: 1.1
Author URI: http://wordpress.org/extend/plugins/profile/zingming
*/

class ShushThatNoise {
      const shortcodeName = "shush";

      function ShushThatNoise () {
      	       $this->__construct();
      }

      function __construct () {
      	       add_shortcode(self::shortcodeName, array($this, 'runShortcode'));
	       add_filter('comment_text', array($this, 'filterComment'));
      }

      function runShortcode ($atts = null, $content = null) {
               extract(shortcode_atts(array(
                        'reason' => '',
               ), $atts));
	       return $this->getOpeningHtmlTags(true, $reason) . $content . $this->getClosingHtmlTags();
      }

      function filterComment ($comment) {
      	       $shortcodeOpeningRegex = '/\[' . self::shortcodeName . '( reason\=\".*\")?\]/';
      	       $shushed = preg_replace_callback(
	       		$shortcodeOpeningRegex,
			array($this,"getCommentOpeningHtmlTags"),
			$comment
	       	); 	

	       $shortcodeClosing = $this->getShortcode(true);
	       $shushed = str_replace($shortcodeClosing, $this->getClosingHtmlTags(), $shushed);

	       return $shushed;
      }

      function getCommentOpeningHtmlTags ($matches) {
      	       $index_of_reason_value = 9;
      	       return $this->getOpeningHtmlTags(false, substr($matches[1], $index_of_reason_value, $matches[1].length - 1));
      }

      function getOpeningHtmlTags ($byAuthor = false, $reason = '') {
      	       return "<div>\n<p>" . $this->getNote($byAuthor, $reason) . '<a href="javascript:;" onclick="' . $this->getOnClickJS() . '">Show</a></p>' . "\n" . '<div style="display:none;">';
      }

      function getClosingHtmlTags () {
      	      return "</div>\n</div>\n";
      }

      function getShortcode ($closing = false) {
      	       if ($closing)
	       	  return '[/' . self::shortcodeName . ']';
	       else
	          return '[' . self::shortcodeName . ']';
      }

      function getOnClickJS () {
      	       return "var noise = this.parentNode.parentNode.getElementsByTagName('div')[0]; if (noise.style.display == 'none') { noise.style.display = ''; this.innerHTML = 'Hide'; noise.style.paddingBottom = '1em'; this.parentNode.style.marginBottom = '0.5em'; } else { noise.style.display = 'none'; this.innerHTML = 'Show'; }";
      }

      function getNote ($byAuthor = false, $reason = '') {
       	       if ($byAuthor)
	       {
			if ($reason && $reason != '')
				return "<em>Spoiler</em> ({$reason}): ";
			else
				return "<em>Spoiler</em>: ";
	       }
	       else if ($reason && $reason != '')
	       {    
			return "<em>Buried Comment</em> (Reason: {$reason}) &nbsp; ";
	       }
	       else
	       {	
      	       		return "<em>Buried Comment:</em> ";
	       }
      }
}

$shushthatnoise = new ShushThatNoise();

?>
