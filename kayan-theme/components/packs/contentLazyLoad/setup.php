<?php 
function YourColor__ContextLazyLoad( $content ) {
  // Replace img src

  // Replace img srcset
  $content = preg_replace( '/(<img[^>]+)(srcset\s*=\s*[\'"]([^"\']*)[\'"])/Ui', '$1data-loader-srcset="$3"', $content );
  
  // Replace iframe src
    // استخراج العناصر <iframe> من المحتوى
    preg_match_all('/<iframe.*?>/', $content, $matches);

    // تحقق مما إذا كانت هناك عناصر <iframe> موجودة
    if (isset($matches[0])) {
        foreach ($matches[0] as $iframe) {
            // استبدال "src=" بـ "data-loader-src" في العنصر <iframe>
            $modified_iframe = str_replace('src=', 'data-loader-src=', $iframe);
            // استبدال العنصر الأصلي بالعنصر المعدل في المحتوى
            $content = str_replace($iframe, $modified_iframe, $content);
        }
    }
  
  return $content;
}

// Apply to post content and widgets
add_filter( 'the_content', 'YourColor__ContextLazyLoad' );
add_filter( 'widget_text', 'YourColor__ContextLazyLoad' );