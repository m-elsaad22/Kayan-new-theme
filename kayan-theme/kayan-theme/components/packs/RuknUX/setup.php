<?php
/**
 * ╔══════════════════════════════════════════════════════════════════╗
 *   RUKN UX PACK — تحسينات تجربة الاستخدام
 *
 *   جدول المحتويات الجانبي (Side TOC):
 *      في صفحات المقالات: تبويب عائم جانبي "جدول المحتويات" يفتح
 *      قائمة منزلقة فيها كل عناوين المقال + مربع بحث، والضغط على أي
 *      عنوان ينقلك له بسلاسة
 * ╚══════════════════════════════════════════════════════════════════╝
 */

class Rukn_UX_Pack {

	public function __construct(){
		add_action( 'wp_head',   array( $this, 'ux_css' ), 98 );
		add_action( 'wp_footer', array( $this, 'ux_markup_js' ), 98 );
	}

	# ════════════════════════════════════════════════════════════════
	# CSS
	# ════════════════════════════════════════════════════════════════
	public function ux_css(){
		echo '<style id="rukn-ux-css">';

			# ═══ جدول المحتويات الجانبي ═══
			# زرار دائري بأيقونة على الحافة اليمنى (فيزيائياً — ثابت مهما كان اتجاه الموقع)
			echo '#rukn-toc-tab{position:fixed;top:42%;right:12px;z-index:99990;width:48px;height:48px;border-radius:50%;background:var(--grad,linear-gradient(135deg,#0A1A33,#14335e));color:#fff;border:none;cursor:pointer;display:grid;place-items:center;font-size:18px;box-shadow:0 10px 26px rgba(10,26,51,.35);transition:transform .25s}';
			echo '#rukn-toc-tab:hover{transform:scale(1.08)}';
			echo '#rukn-toc-tab i{color:var(--aqua,#4FA8FF)}';
			echo '#rukn-toc-tab::after{content:"جدول المحتويات";position:absolute;top:110%;right:50%;transform:translateX(50%);background:#0A1A33;color:#fff;font-family:Cairo,sans-serif;font-size:11px;font-weight:700;padding:4px 10px;border-radius:8px;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .25s}';
			echo '#rukn-toc-tab:hover::after{opacity:1}';
			# اللوحة — تنزلق من اليمين فيزيائياً
			echo '#rukn-toc-panel{position:fixed;top:0;right:0;height:100dvh;width:min(320px,86vw);z-index:99997;background:#fff;box-shadow:-18px 0 50px rgba(10,26,51,.25);display:flex;flex-direction:column;transform:translateX(110%);transition:transform .35s cubic-bezier(.2,.8,.25,1);font-family:Cairo,Tajawal,sans-serif;direction:rtl}';
			echo '#rukn-toc-panel.open{transform:translateX(0)}';
			echo '#rukn-toc-panel .rt-head{display:flex;align-items:center;gap:10px;padding:16px 18px;background:var(--grad,#0A1A33);color:#fff;font-weight:800;font-size:15.5px}';
			echo '#rukn-toc-panel .rt-head i{color:var(--aqua,#4FA8FF)}';
			echo '#rukn-toc-panel .rt-close{margin-inline-start:auto;background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;display:grid;place-items:center}';
			echo '#rukn-toc-panel .rt-search{padding:12px 14px;border-bottom:1px solid #eef2f6}';
			echo '#rukn-toc-panel .rt-search input{width:100%;border:1.5px solid #e3e9f2;border-radius:10px;padding:9px 12px;font-family:Tajawal,sans-serif;font-size:14px;outline:none}';
			echo '#rukn-toc-panel .rt-search input:focus{border-color:var(--blue,#2E9DF7)}';
			echo '#rukn-toc-panel .rt-list{flex:1;overflow-y:auto;padding:10px 8px}';
			echo '#rukn-toc-panel .rt-list a{display:block;padding:10px 14px;border-radius:10px;color:#33415c;font-weight:700;font-size:14px;line-height:1.6;text-decoration:none;transition:.2s}';
			echo '#rukn-toc-panel .rt-list a:hover{background:rgba(46,157,247,.08);color:var(--turq,#1FB5A3)}';
			echo '#rukn-toc-panel .rt-list a.rt-h3{padding-inline-start:30px;font-weight:400;font-size:13.5px;color:#5b6b85}';
			echo '#rukn-toc-backdrop{position:fixed;inset:0;background:rgba(10,26,51,.45);z-index:99996;opacity:0;pointer-events:none;transition:opacity .3s}';
			echo '#rukn-toc-backdrop.open{opacity:1;pointer-events:auto}';
			# في الموبايل: الزرار أصغر وأعلى شوية عشان مايتعارضش مع أزرار الواتساب العائمة
			echo '@media (max-width:768px){#rukn-toc-tab{top:34%;width:44px;height:44px;font-size:16px;right:10px}}';

		echo '</style>';
	}

	# ════════════════════════════════════════════════════════════════
	# الماركب + الجافاسكريبت
	# ════════════════════════════════════════════════════════════════
	public function ux_markup_js(){

		# ═══ جدول المحتويات — صفحات المقالات فقط ═══
		if( is_singular() ){
			echo '<script type="text/javascript">';
				echo '(function(){';
					echo 'var content=document.querySelector(".-single-post-content")||document.querySelector(".article-body");';
					echo 'if(!content)return;';
					echo 'var heads=content.querySelectorAll("h2,h3");';
					echo 'if(heads.length<2)return;';

					# بناء التبويب واللوحة والخلفية
					echo 'var tab=document.createElement("button");tab.id="rukn-toc-tab";tab.setAttribute("aria-label","جدول المحتويات");tab.innerHTML=\'<i class="fas fa-list-ul"></i>\';document.body.appendChild(tab);';
					echo 'var backdrop=document.createElement("div");backdrop.id="rukn-toc-backdrop";document.body.appendChild(backdrop);';
					echo 'var panel=document.createElement("div");panel.id="rukn-toc-panel";';
					echo 'panel.innerHTML=\'<div class="rt-head"><i class="fas fa-list-ul"></i> جدول المحتويات<button class="rt-close" aria-label="إغلاق">✕</button></div><div class="rt-search"><input type="text" placeholder="ابحث في العناوين..."></div><div class="rt-list"></div>\';';
					echo 'document.body.appendChild(panel);';

					# تعبئة العناوين
					echo 'var list=panel.querySelector(".rt-list");';
					echo 'heads.forEach(function(h,i){';
						echo 'if(!h.id)h.id="rukn-h-"+i;';
						echo 'var a=document.createElement("a");';
						echo 'a.href="#"+h.id;';
						echo 'a.textContent=h.textContent.trim();';
						echo 'if(h.tagName==="H3")a.className="rt-h3";';
						echo 'a.addEventListener("click",function(e){';
							echo 'e.preventDefault();close();';
							echo 'var top=h.getBoundingClientRect().top+window.pageYOffset-95;';
							echo 'window.scrollTo({top:top,behavior:"smooth"});';
						echo '});';
						echo 'list.appendChild(a);';
					echo '});';

					# البحث في العناوين
					echo 'panel.querySelector(".rt-search input").addEventListener("input",function(){';
						echo 'var q=this.value.trim();';
						echo 'list.querySelectorAll("a").forEach(function(a){';
							echo 'a.style.display=(q===""||a.textContent.indexOf(q)>-1)?"block":"none";';
						echo '});';
					echo '});';

					# الفتح والغلق
					echo 'function open(){panel.classList.add("open");backdrop.classList.add("open")}';
					echo 'function close(){panel.classList.remove("open");backdrop.classList.remove("open")}';
					echo 'tab.addEventListener("click",open);';
					echo 'backdrop.addEventListener("click",close);';
					echo 'panel.querySelector(".rt-close").addEventListener("click",close);';
				echo '})();';
			echo '</script>';
		}

	}

}
new Rukn_UX_Pack;
